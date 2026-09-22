<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MasterCurrencies Model
 *
 * @property \App\Model\Table\TraineeInstallmentsTable&\Cake\ORM\Association\HasMany $TraineeInstallments
 *
 * @method \App\Model\Entity\MasterCurrency get($primaryKey, $options = [])
 * @method \App\Model\Entity\MasterCurrency newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\MasterCurrency[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MasterCurrency|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterCurrency saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MasterCurrency patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MasterCurrency[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\MasterCurrency findOrCreate($search, callable $callback = null, $options = [])
 */
class MasterCurrenciesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('master_currencies');
        $this->setDisplayField('title');

        $this->hasMany('TraineeInstallments', [
            'foreignKey' => 'master_currency_id',
            'strategy' => 'select',
        ]);
    }

    /**
     * The one currency this installation keeps its books in.
     *
     * Nothing in this system converts anything. There is no exchange rate
     * anywhere, journals carry no currency at all, and every screen writes
     * amounts with an Rp in front of them. So the books are in rupiah, and
     * that is an assumption worth stating out loud rather than leaving
     * implied by the formatting: an amount recorded in another currency is
     * not wrong by a few percent, it is wrong by a factor of a hundred.
     *
     * This is the code that assumption is pinned to. Change it here if the
     * books ever move, and every place that asks follows.
     */
    const BOOK_CURRENCY_CODE = 'IDR';

    /**
     * The id the opening installment row used to be given outright.
     *
     * Set Owing Cost wrote master_currency_id = 66 as a literal, with nothing
     * saying what 66 was. It is kept only as a last resort for an installation
     * whose master_currencies rows have no currency_code filled in.
     */
    const LEGACY_BOOK_CURRENCY_ID = 66;

    /**
     * @var array|null Resolved once per request: ['id' => n, 'code' => s, 'title' => s]
     */
    protected $_bookCurrency = null;

    /**
     * @var array|null Read once per request: id => the code or title to show.
     */
    protected $_labels = null;

    /**
     * The master_currencies row for the book currency, as far as it can be found.
     *
     * Matched on currency_code first, then on a title that names the rupiah,
     * and only then on the id the code used to hard-code. The last of those
     * says so in the returned row, so a caller can tell a real match from a
     * guess.
     *
     * @return array ['id' => int, 'code' => string, 'title' => string, 'resolved' => string]
     */
    public function bookCurrency()
    {
        if ($this->_bookCurrency !== null) {
            return $this->_bookCurrency;
        }

        $found = null;
        try {
            $rows = $this->find()->enableHydration(false)->toArray();
            foreach ($rows as $row) {
                if (strcasecmp(trim((string)$row['currency_code']), self::BOOK_CURRENCY_CODE) === 0) {
                    $found = ['id' => (int)$row['id'], 'code' => (string)$row['currency_code'],
                        'title' => (string)$row['title'], 'resolved' => 'code'];
                    break;
                }
            }
            if (!$found) {
                foreach ($rows as $row) {
                    if (stripos((string)$row['title'], 'rupiah') !== false) {
                        $found = ['id' => (int)$row['id'], 'code' => (string)$row['currency_code'],
                            'title' => (string)$row['title'], 'resolved' => 'title'];
                        break;
                    }
                }
            }
            if (!$found) {
                foreach ($rows as $row) {
                    if ((int)$row['id'] === self::LEGACY_BOOK_CURRENCY_ID) {
                        $found = ['id' => (int)$row['id'], 'code' => (string)$row['currency_code'],
                            'title' => (string)$row['title'], 'resolved' => 'legacy id'];
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            // No master list reachable: the legacy id is all there is to go on.
        }

        if (!$found) {
            $found = ['id' => self::LEGACY_BOOK_CURRENCY_ID, 'code' => self::BOOK_CURRENCY_CODE,
                'title' => self::BOOK_CURRENCY_CODE, 'resolved' => 'assumed'];
        }

        $this->_bookCurrency = $found;

        return $found;
    }

    /**
     * @return int The id to stamp on a row this system records itself.
     */
    public function bookCurrencyId()
    {
        $currency = $this->bookCurrency();

        return $currency['id'];
    }

    /**
     * Is this row's currency one the books can take at face value?
     *
     * An empty currency counts as yes. Most rows in this system were written
     * before anybody chose one, the screens that show them say Rp, and
     * treating them as anything else would recategorise history on a guess.
     * The assumption is deliberate and it is only made here.
     *
     * @param mixed $currencyId master_currency_id, possibly null.
     * @return bool
     */
    public function isBookCurrency($currencyId)
    {
        if ($currencyId === null || $currencyId === '' || (int)$currencyId === 0) {
            return true;
        }

        return (int)$currencyId === $this->bookCurrencyId();
    }

    /**
     * Same question asked of a currency code rather than an id, for the tables
     * that store the code as text instead of pointing at the master list.
     *
     * @param mixed $code Currency code, possibly null or blank.
     * @return bool
     */
    public function isBookCurrencyCode($code)
    {
        $code = trim((string)$code);
        if ($code === '') {
            return true;
        }

        return strcasecmp($code, self::BOOK_CURRENCY_CODE) === 0;
    }

    /**
     * What to call a currency on screen, given its id.
     *
     * The whole list is read once and kept, because the callers ask this per
     * row: a page listing three hundred payments would otherwise make three
     * hundred queries to print a three-letter code.
     *
     * @param mixed $currencyId master_currency_id, possibly null.
     * @return string A code, a title, or the id in the last resort.
     */
    public function labelFor($currencyId)
    {
        if ($currencyId === null || (int)$currencyId === 0) {
            return self::BOOK_CURRENCY_CODE;
        }

        if ($this->_labels === null) {
            $this->_labels = [];
            try {
                foreach ($this->find()->enableHydration(false) as $row) {
                    $code = trim((string)$row['currency_code']);
                    $this->_labels[(int)$row['id']] = $code !== '' ? $code : (string)$row['title'];
                }
            } catch (\Exception $e) {
                // Nothing to look names up in; the ids below say so plainly.
            }
        }

        $id = (int)$currencyId;

        return isset($this->_labels[$id]) ? $this->_labels[$id] : '#' . $id;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        // Auto-increment primary key: the database supplies it, so no form
        // ever sends one. Requiring it on create made save() refuse every add
        // while edit carried on working, and refuse it the way save() always
        // does - returning false with the reason inside the entity, where the
        // controller's generic "could not be saved" never showed it. See
        // bin/check-create-validation.php, which is what found this.
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->allowEmptyString('title');

        $validator
            ->scalar('currency_code')
            ->maxLength('currency_code', 255)
            ->allowEmptyString('currency_code');

        $validator
            ->scalar('country')
            ->maxLength('country', 255)
            ->allowEmptyString('country');

        return $validator;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_trainee_accountings';
    }
}
