<?php
namespace App\Controller;

/**
 * The rows behind a related-records tab on a detail page.
 *
 * Element/related_records_table_static.ctp puts a tab on a record's page for
 * each table that refers to it, and asks that table's own controller for the
 * rows. There were nine copies of this method, one per controller, each about
 * ninety lines and differing only in the model it queried - and seventeen more
 * links pointing at controllers that had no copy at all, where the tab could
 * only ever be empty and nothing said why.
 *
 * Worse, every copy read the column to filter on straight out of the query
 * string:
 *
 *     $query->where([$filterField => $filterValue]);
 *
 * CakePHP puts an array key into the SQL as written. A key of
 * "1 = 1 OR id IS NOT" produces WHERE 1 = 1 or id is not :c0, which matches
 * every row: anyone who could reach the endpoint could read the whole table
 * rather than the rows belonging to the record on screen. The column filters
 * went the same way.
 *
 * So there is one copy now, and every column name it uses - the one it filters
 * on and every one a filter names - has to be a column of that table before it
 * reaches a query. A name that is not one is refused and reported back rather
 * than dropped, so a mis-spelt tab says so instead of showing nothing.
 */
trait RelatedRecordsTrait
{
    /**
     * Rows of this controller's own table that belong to one parent record.
     *
     * @return \Cake\Http\Response JSON: success, records, pagination, and any
     *  column name it refused.
     */
    public function getRelated()
    {
        $this->autoRender = false;
        $this->response = $this->response
            ->withType('application/json')
            ->withCharset('UTF-8');

        try {
            $table = $this->loadModel($this->modelClass);
        } catch (\Exception $e) {
            return $this->relatedRecordsAnswer([
                'success' => false,
                'error' => 'This controller has no table to read.',
            ]);
        }

        $schema = $table->getSchema();
        $field = (string)$this->request->getQuery('filter_field');
        $value = $this->request->getQuery('filter_value');

        if ($field === '' || !$schema->hasColumn($field)) {
            return $this->relatedRecordsAnswer([
                'success' => false,
                'error' => sprintf('%s has no column %s.', $table->getAlias(),
                    $field === '' ? '(none given)' : $field),
            ]);
        }

        $page = max(1, (int)$this->request->getQuery('page', 1));
        $limit = min(100, max(1, (int)$this->request->getQuery('limit', 50)));

        $query = $table->find()->where([$table->aliasField($field) => $value]);

        $refused = [];
        foreach ($this->relatedRecordsFilters() as $column => $filter) {
            if (!$schema->hasColumn($column)) {
                $refused[] = $column;
                continue;
            }
            $this->relatedRecordsFilter($query, $table->aliasField($column), $filter);
        }

        $total = $query->count();
        $records = $query->limit($limit)->offset(($page - 1) * $limit)->toArray();

        // A path in the database is not a file on disk. The table says whether
        // each one is still there, so a tab can show the same "missing" marker
        // the server-rendered table does rather than a broken image.
        foreach ($records as $record) {
            foreach (array_keys($record->toArray()) as $column) {
                if (!preg_match('/(image|photo|file|document)/i', $column)) {
                    continue;
                }
                $path = $record->get($column);
                if (empty($path) || !is_string($path)) {
                    continue;
                }
                $record->set($column . '_exists',
                    file_exists(WWW_ROOT . ltrim(str_replace('/', DS, $path), DS)));
            }
        }

        return $this->relatedRecordsAnswer([
            'success' => true,
            'records' => $records,
            'refused' => $refused,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => $limit > 0 ? (int)ceil($total / $limit) : 0,
            ],
        ]);
    }

    /**
     * The column filters the tab's own filter row sent, as an array.
     *
     * @return array column => ['value' => ..., 'operator' => ...]
     */
    protected function relatedRecordsFilters()
    {
        $json = $this->request->getQuery('filters');
        if (!is_string($json) || $json === '') {
            return [];
        }
        $filters = json_decode($json, true);

        return is_array($filters) ? $filters : [];
    }

    /**
     * Add one column filter to the query.
     *
     * The column is already known to be a column of this table; only the
     * operator is chosen here, and an operator that is not on the list is
     * treated as "contains" rather than passed on.
     *
     * @param \Cake\ORM\Query $query The query.
     * @param string $column Alias.column, already checked.
     * @param array $filter What the filter row sent for it.
     * @return void
     */
    protected function relatedRecordsFilter($query, $column, array $filter)
    {
        $value = isset($filter['value']) ? $filter['value'] : '';
        if ($value === '' || $value === null) {
            return;
        }
        $operator = isset($filter['operator']) ? $filter['operator'] : 'contains';

        switch ($operator) {
            case 'equals':
                $query->where([$column => $value]);
                break;
            case 'starts_with':
                $query->where([$column . ' LIKE' => $value . '%']);
                break;
            case 'ends_with':
                $query->where([$column . ' LIKE' => '%' . $value]);
                break;
            case 'greater_than':
                $query->where([$column . ' >' => $value]);
                break;
            case 'less_than':
                $query->where([$column . ' <' => $value]);
                break;
            case 'not_empty':
                $query->where([$column . ' IS NOT' => null]);
                break;
            default:
                $query->where([$column . ' LIKE' => '%' . $value . '%']);
        }
    }

    /**
     * Write the answer out as JSON.
     *
     * @param array $body What to send.
     * @return \Cake\Http\Response
     */
    protected function relatedRecordsAnswer(array $body)
    {
        return $this->response->withStringBody(json_encode($body));
    }
}
