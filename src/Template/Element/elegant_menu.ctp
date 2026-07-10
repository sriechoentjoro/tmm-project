<!-- Elegant Tab Menu Navigation -->
<style>
body, html {
    overflow-x: hidden;
    overflow-y: auto;
    max-width: 100vw;
}

.elegant-menu-wrapper {
    background: linear-gradient(135deg, #00BCD4 0%, #00838F 100%);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    margin-bottom: 30px;
    position: relative;
    overflow: visible !important;
    min-height: 60px;
}

.elegant-menu-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
    background-size: cover;
    opacity: 0.3;
    pointer-events: none;
}

.elegant-tabs {
    display: flex;
    overflow-x: auto;
    overflow-y: visible !important;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
    list-style: none;
    margin: 0;
    padding: 0 8px;
    box-sizing: border-box;
    position: relative;
    scrollbar-width: thin;
    scrollbar-color: transparent transparent;
    cursor: grab;
    /* Allow native horizontal pan on touch devices */
    touch-action: pan-x;
}

.elegant-tabs:active {
    cursor: grabbing;
}

.elegant-tabs::-webkit-scrollbar {
    height: 4px;
}

.elegant-tabs::-webkit-scrollbar-track {
    background: transparent;
}

.elegant-tabs::-webkit-scrollbar-thumb {
    background: transparent;
    border-radius: 2px;
}

.elegant-tabs::-webkit-scrollbar-thumb:hover {
    background: transparent;
}

.elegant-tab {
    flex: 0 0 auto;
    min-width: 150px;
    position: relative;
    z-index: 1;
    overflow: visible !important;
}

.elegant-tab.active {
    z-index: 1000;
    overflow: visible !important;
}

.elegant-tab > a {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 10px 20px 14px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    position: relative;
    white-space: nowrap;
}

.elegant-tab > a i {
    margin-right: 8px;
    font-size: 18px;
}

.elegant-tab > a:hover {
    color: #fff;
    background: rgba(255,255,255,0.1);
    border-bottom-color: rgba(255,255,255,0.5);
}

.elegant-tab.active > a {
    color: #fff;
    background: rgba(255,255,255,0.15);
    border-bottom-color: #fff;
}

ul.elegant-submenu {
    background: #fff;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-radius: 0 0 12px 12px;
    padding: 12px;
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 250px;
    max-width: 600px;
    z-index: 99999;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 5px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    transform: translateY(-10px);
    display: none !important;
    list-style: none;
    margin: 0;
    pointer-events: auto;
}

li.elegant-tab.active > ul.elegant-submenu {
    display: grid !important;
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateX(-50%) !important;
    pointer-events: auto !important;
}

li.elegant-submenu-item,
li.elegant-submenu-item a {
    pointer-events: auto !important;
    position: relative;
    z-index: 1;
}

li.elegant-submenu-item {
    list-style: none;
    display: block;
}

li.elegant-submenu-item a {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    color: #4a5568;
    text-decoration: none;
    font-size: 13px;
    border-radius: 6px;
    transition: all 0.2s ease;
    border-left: 2px solid transparent;
}

li.elegant-submenu-item a i {
    margin-right: 8px;
    color: #00BCD4;
    font-size: 16px;
    width: 20px;
    text-align: center;
}

li.elegant-submenu-item a:hover {
    background: linear-gradient(135deg, #f0f4ff 0%, #e9f0ff 100%);
    border-left-color: #00BCD4;
    color: #00BCD4;
    transform: translateX(3px);
}

li.elegant-submenu-item a:hover i {
    color: #0097A7;
}

li.elegant-tab-divider {
    flex: 0 0 auto;
    width: 1px;
    align-self: stretch;
    margin: 10px 8px;
    background: rgba(255,255,255,0.35);
    list-style: none;
}

li.elegant-category-group {
    flex: 0 0 auto;
    display: flex;
    flex-direction: column;
    list-style: none;
    overflow: visible !important;
    /* padding-left (not margin-left) so the buffer lives INSIDE the element
       and is never clipped when the element's border is at or past the left edge */
    padding-left: 20px;
    margin-left: 0;
}

.elegant-category-label {
    color: rgba(255,255,255,0.7);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    text-align: left;
    padding: 8px 12px 0;
    white-space: nowrap;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    margin: 0 8px;
    padding-bottom: 4px;
}

ul.elegant-category-tabs {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    overflow: visible !important;
}

.elegant-menu-hint {
    position: absolute;
    top: 4px;
    right: 10px;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 6px;
    color: rgba(255,255,255,0.9);
    font-size: 11px;
    background: rgba(0,0,0,0.25);
    border-radius: 12px;
    padding: 3px 12px;
    pointer-events: none;
    opacity: 0.95;
    transition: opacity 0.8s ease;
    max-width: 60vw;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.elegant-menu-hint.hint-faded {
    opacity: 0;
}

.elegant-menu-wrapper:hover .elegant-menu-hint.hint-faded {
    opacity: 0.95;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .elegant-menu-wrapper {
        overflow-x: hidden;
        max-width: 100vw;
    }

    .elegant-tabs {
        width: 100%;
        flex-wrap: nowrap;
    }

    .elegant-tab {
        flex: 0 0 auto;
        min-width: 130px;
    }

    .elegant-tab > a {
        font-size: 13px;
        padding: 8px 12px 12px;
    }

    .elegant-category-label {
        font-size: 9px;
        letter-spacing: 1px;
    }

    /* Touch devices swipe natively - hide the mouse-wheel hint */
    .elegant-menu-hint {
        display: none;
    }

    .elegant-tab.active .elegant-submenu {
        left: 50% !important;
        right: auto !important;
        transform: translateX(-50%) !important;
        width: 90vw !important;
        max-width: 90vw !important;
        grid-template-columns: 1fr !important;
        box-sizing: border-box;
        margin-left: 0 !important;
    }
}
</style>

<?php
    $menus = isset($navigationMenus) ? $navigationMenus : [];
    $allowedControllers = isset($allowedControllers) ? $allowedControllers : [];
    $rolePermissions = isset($rolePermissions) ? $rolePermissions : [];
    $isAdministrator = isset($isAdministrator) ? $isAdministrator : false;
    
    /**
     * Check if user has permission to access a menu URL with specific action
     */
    function hasMenuPermissionWithAction($menuUrl, $rolePermissions, $isAdministrator) {
        if ($isAdministrator) {
            return true; // Administrator sees all menus
        }
        
        if (empty($menuUrl) || $menuUrl === '#' || $menuUrl === 'javascript:void(0)') {
            return false; // Don't show parent menus without URL
        }
        
        // Extract controller and action from URL
        $urlParts = array_values(array_filter(explode('/', trim($menuUrl, '/'))));
        
        // Find controller name and action (skip 'tmm' if present)
        $controller = null;
        $action = 'index'; // Default action if not specified
        
        $skip = ['tmm', 'localhost', 'http:', 'https:'];
        $i = 0;
        foreach ($urlParts as $part) {
            if (!in_array($part, $skip) && !empty($part)) {
                if ($i === 0) {
                    $controller = $part;
                    $i++;
                } elseif ($i === 1) {
                    $action = $part;
                    break;
                }
            }
        }
        
        if (empty($controller)) {
            return true; // Allow if controller not found
        }
        
        // Convert URL format to Controller name; dashes must become
        // underscores first (e.g. 'acceptance-organizations' -> 'AcceptanceOrganizations')
        $controllerName = \Cake\Utility\Inflector::camelize(str_replace('-', '_', $controller));

        // Check if controller exists in permissions
        if (!isset($rolePermissions[$controllerName])) {
            return false;
        }

        $allowedActions = $rolePermissions[$controllerName];

        // Check if action is allowed
        // '*' means all actions allowed
        if (in_array('*', $allowedActions)) {
            return true;
        }

        // Check specific action; URL actions are dashed, permission
        // entries are camelBacked (e.g. 'promote-to-trainee' -> 'promoteToTrainee')
        $actionName = \Cake\Utility\Inflector::variable(str_replace('-', '_', $action));
        $allowed = in_array($action, $allowedActions) || in_array($actionName, $allowedActions);

        return $allowed;
    }
    
    /**
     * Filter child menus based on permissions
     */
    function filterChildMenusWithAction($childMenus, $rolePermissions, $isAdministrator) {
        $filtered = [];
        foreach ($childMenus as $child) {
            if (hasMenuPermissionWithAction($child->url, $rolePermissions, $isAdministrator)) {
                $filtered[] = $child;
            }
        }
        return $filtered;
    }
?>

<?php
    // $allowedMenuIds is the explicit set of menu IDs (and their parents) the
    // current role has in role_menus. When set (non-administrators), we use an
    // exact ID match instead of the loose URL-parsed controller check so that
    // sibling menu items sharing a controller (e.g. every Dashboard::* sub-page)
    // are NOT shown just because one of them was granted to the role.
    $useIdFilter = !$isAdministrator && isset($allowedMenuIds) && is_array($allowedMenuIds);

    // First pass: apply permission filtering, then group visible menus by
    // category so each group can render a label above its tabs
    $categoryGroups = [];
    foreach ($menus as $menu) {
        if ($useIdFilter) {
            // Show parent only if it was explicitly assigned (or is a parent of an assigned child)
            if (!in_array($menu->id, $allowedMenuIds)) {
                continue;
            }
            // Filter children to only those explicitly assigned
            $filteredChildren = [];
            if (!empty($menu->child_menus)) {
                foreach ($menu->child_menus as $child) {
                    if (in_array($child->id, $allowedMenuIds)) {
                        $filteredChildren[] = $child;
                    }
                }
            }
        } else {
            // Administrator or no ID list: fall back to URL-parsed controller check
            $filteredChildren = !empty($menu->child_menus)
                ? filterChildMenusWithAction($menu->child_menus, $rolePermissions, $isAdministrator)
                : [];

            $showParent = $isAdministrator || !empty($filteredChildren);
            if (!$isAdministrator && empty($filteredChildren)) {
                $parentHasUrl = !empty($menu->url) && $menu->url !== '#' && $menu->url !== 'javascript:void(0)';
                if ($parentHasUrl) {
                    $showParent = hasMenuPermissionWithAction($menu->url, $rolePermissions, $isAdministrator);
                }
            }
            if (!$showParent) {
                continue;
            }
        }

        $menuCategory = isset($menu->category) && $menu->category !== '' ? $menu->category : 'Other';
        $categoryGroups[$menuCategory][] = ['menu' => $menu, 'children' => $filteredChildren];
    }
?>

<div class="elegant-menu-wrapper">
    <ul class="elegant-tabs">
        <?php $isFirstGroup = true; ?>
        <?php foreach ($categoryGroups as $categoryName => $groupItems): ?>
            <?php if (!$isFirstGroup): ?>
                <li class="elegant-tab-divider"></li>
            <?php endif; $isFirstGroup = false; ?>
            <li class="elegant-category-group" data-category="<?= h($categoryName) ?>">
                <div class="elegant-category-label"><?= h($categoryName) ?></div>
                <ul class="elegant-category-tabs">
                <?php foreach ($groupItems as $groupItem): ?>
                    <?php
                        $menu = $groupItem['menu'];
                        $filteredChildren = $groupItem['children'];
                    ?>
            <li class="elegant-tab" data-menu-id="<?= $menu->id ?>" data-category="<?= h($categoryName) ?>">
                <?php
                    // Parent tabs that have visible children are navigation containers only —
                    // their stored URL must not be navigable (the JS handles opening the submenu).
                    // Using '#' prevents direct URL access even via right-click → open in new tab.
                    if (!empty($filteredChildren)) {
                        $menuUrl = '#';
                    } else {
                        $menuUrl = $menu->url ?: 'javascript:void(0)';
                    }
                    if ($menuUrl !== 'javascript:void(0)' && $menuUrl !== '#' && strpos($menuUrl, 'http') !== 0 && strpos($menuUrl, '/') === 0) {
                        // Relative URL starting with / - add project folder
                        $menuUrl = $this->request->getAttribute('webroot') . ltrim($menuUrl, '/');
                    }
                ?>
                <a href="<?= $menuUrl ?>">
                    <?php if ($menu->icon): ?>
                        <i class="fas <?= h($menu->icon) ?>"></i>
                    <?php endif; ?>
                    <span><?= h(__($menu->title)) ?></span>
                </a>

                <?php if (!empty($filteredChildren)): ?>
                    <ul class="elegant-submenu">
                        <?php foreach ($filteredChildren as $child): ?>
                            <li class="elegant-submenu-item">
                                <?php 
                                    // Add project folder to relative URLs
                                    $childUrl = $child->url;
                                    if ($childUrl && strpos($childUrl, 'http') !== 0 && strpos($childUrl, '/') === 0) {
                                        // Relative URL starting with / - add project folder
                                        $childUrl = $this->request->getAttribute('webroot') . ltrim($childUrl, '/');
                                    }
                                ?>
                                <a href="<?= h($childUrl) ?>" target="<?= h($child->target) ?>">
                                    <?php if ($child->icon): ?>
                                        <i class="fas <?= h($child->icon) ?>"></i>
                                    <?php endif; ?>
                                    <span><?= h(__($child->title)) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
                <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="elegant-menu-hint" id="elegantMenuHint">
        <i class="fas fa-arrows-alt-h"></i>
        <span>Hover over the menu bar and scroll the mouse wheel &mdash; or click and drag &mdash; to slide the menu left / right</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Elegant menu initialized');
    
    const tabs = document.querySelectorAll('.elegant-tab');
    console.log('Found tabs:', tabs.length);

    tabs.forEach((tab, index) => {
        const link = tab.querySelector('a');
        const submenu = tab.querySelector('.elegant-submenu');

        console.log('Tab ' + index + ':', {
            hasLink: !!link,
            hasSubmenu: !!submenu,
            submenuChildren: submenu ? submenu.children.length : 0
        });

        if (submenu && submenu.children.length > 0) {
            // Add down arrow indicator for menus with children
            const arrow = document.createElement('i');
            arrow.className = 'fas fa-chevron-down';
            arrow.style.marginLeft = '5px';
            arrow.style.fontSize = '12px';
            arrow.style.transition = 'transform 0.3s ease';
            link.appendChild(arrow);

            // Click event to toggle submenu
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const isActive = tab.classList.contains('active');

                // Close all other tabs (class only — CSS handles hide via !important)
                tabs.forEach(t => {
                    if (t !== tab) {
                        t.classList.remove('active');
                        const otherArrow = t.querySelector('.fa-chevron-down');
                        if (otherArrow) otherArrow.style.transform = 'rotate(0deg)';
                    }
                });

                if (isActive) {
                    tab.classList.remove('active');
                    arrow.style.transform = 'rotate(0deg)';
                } else {
                    const rect = tab.getBoundingClientRect();
                    submenu.style.position = 'fixed';
                    submenu.style.top = rect.bottom + 'px';
                    // display/opacity/visibility/transform handled by CSS via .active class.
                    // Adding the class triggers layout (display:grid), so the submenu's
                    // real rendered width is now available via getBoundingClientRect().
                    tab.classList.add('active');
                    arrow.style.transform = 'rotate(180deg)';

                    // Clamp so the submenu never extends past either edge of the screen.
                    // The submenu is centered under the tab via translateX(-50%), so we
                    // measure its actual width (varies with column count, 250-600px) and
                    // clamp the center point accordingly.
                    const submenuWidth = submenu.getBoundingClientRect().width;
                    const halfWidth = submenuWidth / 2;
                    const centerX = rect.left + rect.width / 2;
                    const minCenter = halfWidth + 10;
                    const maxCenter = window.innerWidth - halfWidth - 10;
                    submenu.style.left = Math.max(minCenter, Math.min(maxCenter, centerX)) + 'px';
                }
            });
        }
    });

    // Close submenu when clicking outside (class only — CSS !important handles hide)
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.elegant-menu-wrapper')) {
            tabs.forEach(tab => {
                tab.classList.remove('active');
                const arrow = tab.querySelector('.fa-chevron-down');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            });
        }
    });

    // Prevent the document click from closing the menu when clicking inside submenu.
    // Do NOT call e.preventDefault() here — links must still navigate.
    document.querySelectorAll('.elegant-submenu').forEach(submenu => {
        submenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    // ── Initialise tab container ──────────────────────────────────────────
    const tabsContainer = document.querySelector('.elegant-tabs');
    if (tabsContainer) {
        // Override any CSS that centres or indents the tabs (e.g. Bootstrap ul padding,
        // layout justify-content:center) so the first category is always flush-left.
        tabsContainer.style.setProperty('padding', '0', 'important');
        tabsContainer.style.setProperty('margin', '0', 'important');
        tabsContainer.style.setProperty('justify-content', 'flex-start', 'important');

        // Remove the indent from the FIRST category group only;
        // subsequent groups keep their padding-left as a visual gap after the divider.
        const firstGroup = tabsContainer.querySelector('.elegant-category-group');
        if (firstGroup) {
            firstGroup.style.setProperty('padding-left', '0', 'important');
        }

        // Force scroll to the very beginning — browser may restore a previous position
        // or a layout shift can leave scrollLeft > 0.
        tabsContainer.scrollLeft = 0;
        requestAnimationFrame(() => { tabsContainer.scrollLeft = 0; });
    }

    // ── Drag scroll ───────────────────────────────────────────────────────
    let isDown = false;
    let startX;
    let scrollLeft;

    tabsContainer.addEventListener('mousedown', (e) => {
        // Only enable drag on the container, not on links
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            return;
        }
        isDown = true;
        tabsContainer.classList.add('active');
        startX = e.pageX - tabsContainer.offsetLeft;
        scrollLeft = tabsContainer.scrollLeft;
    });

    tabsContainer.addEventListener('mouseleave', () => {
        isDown = false;
        tabsContainer.classList.remove('active');
    });

    tabsContainer.addEventListener('mouseup', () => {
        isDown = false;
        tabsContainer.classList.remove('active');
    });

    tabsContainer.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - tabsContainer.offsetLeft;
        const walk = (x - startX) * 2; // Scroll speed multiplier
        tabsContainer.scrollLeft = scrollLeft - walk;
    });

    // Mouse wheel: hovering over the menu bar and scrolling the wheel
    // slides the menu horizontally instead of scrolling the page
    tabsContainer.addEventListener('wheel', (e) => {
        const delta = Math.abs(e.deltaY) > Math.abs(e.deltaX) ? e.deltaY : e.deltaX;
        if (tabsContainer.scrollWidth > tabsContainer.clientWidth) {
            e.preventDefault();
            tabsContainer.scrollLeft += delta;
        }
    }, { passive: false });

    // Touch swipe — native pan-x handles the scroll animation freely.

    // Fade the usage hint after a few seconds; it reappears while hovering the menu bar
    const menuHint = document.getElementById('elegantMenuHint');
    if (menuHint) {
        setTimeout(() => {
            menuHint.classList.add('hint-faded');
        }, 8000);
    }
});
</script>
