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
    padding: 0;
    position: relative;
    z-index: 1;
    scrollbar-width: thin;
    scrollbar-color: transparent transparent;
    cursor: grab;
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
    justify-content: center;
    padding: 18px 20px;
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
    transform: translateY(0) !important;
    pointer-events: auto !important;
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
        padding: 15px 12px;
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
        
        // Convert URL format to Controller name (e.g., 'candidates' -> 'Candidates')
        $controllerName = \Cake\Utility\Inflector::camelize($controller);
        
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
        
        // Check specific action
        $allowed = in_array($action, $allowedActions);
        
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

<div class="elegant-menu-wrapper">
    <ul class="elegant-tabs">
        <?php foreach ($menus as $menu): ?>
            <?php
                // Filter child menus by permission (action-level)
                $filteredChildren = !empty($menu->child_menus) 
                    ? filterChildMenusWithAction($menu->child_menus, $rolePermissions, $isAdministrator)
                    : [];
                
                // Only show parent menu if:
                // 1. Administrator (sees everything), OR
                // 2. At least one child menu is accessible
                $showParent = $isAdministrator || !empty($filteredChildren);
                
                // Also check if parent has its own URL (not just '#')
                if (!$isAdministrator && empty($filteredChildren)) {
                    $parentHasUrl = !empty($menu->url) && $menu->url !== '#' && $menu->url !== 'javascript:void(0)';
                    if ($parentHasUrl) {
                        $showParent = hasMenuPermissionWithAction($menu->url, $rolePermissions, $isAdministrator);
                    }
                }
                
                if (!$showParent) {
                    continue; // Skip this menu entirely
                }
            ?>
            <li class="elegant-tab" data-menu-id="<?= $menu->id ?>">
                <?php 
                    // Add project folder to relative URLs
                    $menuUrl = $menu->url ?: 'javascript:void(0)';
                    if ($menuUrl !== 'javascript:void(0)' && $menuUrl !== '#' && strpos($menuUrl, 'http') !== 0 && strpos($menuUrl, '/') === 0) {
                        // Relative URL starting with / - add project folder
                        $menuUrl = $this->request->getAttribute('webroot') . ltrim($menuUrl, '/');
                    }
                ?>
                <a href="<?= $menuUrl ?>">
                    <?php if ($menu->icon): ?>
                        <i class="fas <?= h($menu->icon) ?>"></i>
                    <?php endif; ?>
                    <span><?= h($menu->title) ?></span>
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
                                    <span><?= h($child->title) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
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
                console.log('Tab clicked:', tab);
                console.log('Submenu element:', submenu);
                e.preventDefault();
                e.stopPropagation();

                const isActive = tab.classList.contains('active');
                console.log('Is active:', isActive);
                console.log('Current submenu display:', submenu.style.display);

                // Close all other tabs
                tabs.forEach(t => {
                    if (t !== tab) {
                        t.classList.remove('active');
                        const otherSubmenu = t.querySelector('.elegant-submenu');
                        const otherArrow = t.querySelector('.fa-chevron-down');
                        if (otherSubmenu) {
                            otherSubmenu.style.display = 'none';
                            otherSubmenu.style.opacity = '0';
                            otherSubmenu.style.visibility = 'hidden';
                            otherSubmenu.style.transform = 'translateY(-10px)';
                        }
                        if (otherArrow) {
                            otherArrow.style.transform = 'rotate(0deg)';
                        }
                    }
                });

                // Toggle current tab
                if (isActive) {
                    console.log('Closing submenu...');
                    tab.classList.remove('active');
                    submenu.style.display = 'none';
                    submenu.style.opacity = '0';
                    submenu.style.visibility = 'hidden';
                    submenu.style.transform = 'translateY(-10px)';
                    submenu.style.position = 'absolute';
                    arrow.style.transform = 'rotate(0deg)';
                    console.log('Closed - display:', submenu.style.display);
                } else {
                    console.log('Opening submenu...');
                    tab.classList.add('active');
                    
                    // Get position of tab for submenu placement
                    const rect = tab.getBoundingClientRect();
                    console.log('Tab position:', rect);
                    
                    // Position submenu directly below the tab, centered under it
                    submenu.style.position = 'fixed';
                    submenu.style.top = rect.bottom + 'px';
                    submenu.style.left = (rect.left + rect.width / 2) + 'px';
                    submenu.style.transform = 'translateX(-50%)';
                    submenu.style.display = 'grid';
                    submenu.style.opacity = '1';
                    submenu.style.visibility = 'visible';
                    arrow.style.transform = 'rotate(180deg)';
                    console.log('Opened - display:', submenu.style.display);
                }
            });
        } else if (link) {
            // For tabs without submenu, allow normal navigation
            console.log('Tab has no submenu, allowing normal navigation');
        }
    });

    // Close submenu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.elegant-menu-wrapper')) {
            console.log('Clicked outside menu');
            tabs.forEach(tab => {
                tab.classList.remove('active');
                const submenu = tab.querySelector('.elegant-submenu');
                const arrow = tab.querySelector('.fa-chevron-down');
                if (submenu) {
                    submenu.style.display = 'none';
                    submenu.style.opacity = '0';
                    submenu.style.visibility = 'hidden';
                    submenu.style.transform = 'translateY(-10px)';
                }
                if (arrow) {
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        }
    });

    // Prevent closing when clicking inside submenu
    document.querySelectorAll('.elegant-submenu').forEach(submenu => {
        submenu.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('Clicked inside submenu');
        });
    });

    // Drag scroll functionality
    const tabsContainer = document.querySelector('.elegant-tabs');
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
    });

    tabsContainer.addEventListener('mouseup', () => {
        isDown = false;
    });

    tabsContainer.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - tabsContainer.offsetLeft;
        const walk = (x - startX) * 2; // Scroll speed multiplier
        tabsContainer.scrollLeft = scrollLeft - walk;
    });
});
</script>
