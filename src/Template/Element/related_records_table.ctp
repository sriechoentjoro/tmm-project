<!-- AJAX Tab Loader - With Horizontal Scroll + Filters + Pagination -->
<?php
$tabId = isset($tabId) ? $tabId : 'tab-related';
$ajaxUrl = isset($ajaxUrl) ? $ajaxUrl : '';
$filterField = isset($filterField) ? $filterField : '';
$filterValue = isset($filterValue) ? $filterValue : '';
$controller = isset($controller) ? $controller : 'Candidates';
$columns = isset($columns) ? $columns : array();
$title = isset($title) ? $title : 'Related Records';
?>

<div id="<?= h($tabId) ?>-ajax" class="ajax-table-container" data-ajax-url="<?= h($ajaxUrl) ?>" data-filter-field="<?= h($filterField) ?>" data-filter-value="<?= h($filterValue) ?>" data-controller="<?= h($controller) ?>" data-columns='<?= json_encode($columns) ?>'>
    <h4><?= h($title) ?></h4>
    <div class="loading text-center" style="padding:40px">
        <i class="fas fa-spinner fa-spin fa-3x"></i>
        <p>Loading data...</p>
    </div>
    <div class="content" style="display:none"></div>
</div>

/**
 * AJAX Lazy-Loading Table Element
 * Version: 2.0 - Fixed pagination syntax (no space before ternary)
 * Generated: <?= date('Y-m-d H:i:s') ?>
 */
?>
<!-- Cache Buster: Generated at <?= date('Y-m-d H:i:s') ?> -->
<style>
.ajax-table-container {
    margin: 20px 0;
.ajax-table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 15px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
.ajax-table {
    width: 100%;
    min-width: 800px;
    margin: 0;
    border-collapse: collapse;
.ajax-table th, .ajax-table td {
    padding: 8px 12px;
    border: 1px solid #ddd;
    white-space: nowrap;
.ajax-table td img {
    display: block;
    margin: 0 auto;
.ajax-table td a {
    text-decoration: none;
.ajax-table thead th {
    background: #f8f9fa;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
.ajax-filter-row select {
    width: 100%;
    padding: 6px 8px;
    font-size: 13px;
    border: 1px solid #ccc;
    border-radius: 3px;
    background: white;
    margin-bottom: 5px;
.ajax-filter-row input {
    width: 100%;
    min-width: 120px;
    padding: 6px 8px;
    font-size: 13px;
    border: 1px solid #007bff;
    border-radius: 3px;
    background: white;
    box-sizing: border-box;
.ajax-filter-row input:focus {
    outline: none;
    border-color: #0056b3;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
.ajax-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
    border-top: 1px solid #ddd;
.ajax-pagination button {
    padding: 5px 15px;
    margin: 0 5px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
.ajax-pagination button:disabled {
    background: #ccc;
    cursor: not-allowed;
.ajax-pagination button:hover:not(:disabled) {
    background: #0056b3;
</style>

<script>
console.log('ðŸš€ AJAX Element Loading - Version 2.0 - <?= date('H:i:s') ?>');
(function(){
var c=document.getElementById('<?= h($tabId) ?>-ajax');
if(!c){console.error('âŒ Container not found for <?= h($tabId) ?>-ajax');return;}
var tabPane=document.getElementById('<?= h($tabId) ?>-pane');
var loaded=false,currentPage=1,limit=50,totalRecords=0,columnFilters={};

// Event delegation for pagination buttons (attach ONCE at container level)
c.addEventListener('click',function(e){
if(e.target.classList.contains('page-btn')&&!e.target.disabled){
var page=parseInt(e.target.dataset.page);
console.log('ðŸ“„ Pagination clicked, loading page:',page);
load(page);
});

function buildUrl(page){
var u=c.dataset.ajaxUrl+'?filterField='+encodeURIComponent(c.dataset.filterField)+'&filterValue='+encodeURIComponent(c.dataset.filterValue)+'&page='+page+'&limit='+limit;
if(Object.keys(columnFilters).length>0){
u+='&filters='+encodeURIComponent(JSON.stringify(columnFilters));
return u;

function load(page){
if(!loaded){loaded=true;}
page=page||currentPage;
var l=c.querySelector('.loading'),d=c.querySelector('.content');
if(l)l.style.display='block';if(d)d.style.display='none';

console.log('ðŸ”„ Fetching page:',page,'Filters:',columnFilters);
console.log('ðŸŒ URL:',buildUrl(page));
fetch(buildUrl(page)).then(function(r){
console.log('ðŸ“¡ Response status:',r.status,r.statusText);
if(!r.ok)throw new Error('HTTP '+r.status);
return r.json();
}).then(function(data){
console.log('ðŸ“¦ Raw Data:',data);
console.log('âœ… Success:',data.success);
console.log('ðŸ“Š Data array:',data.data);
console.log('ðŸ“ Data length:',data.data ? data.data.length : 0);
if(l)l.style.display='none';

if(data.success&&data.data){
console.log('âœ… Entering render block');
totalRecords=data.pagination.total||0;
currentPage=page;
var cols=JSON.parse(c.dataset.columns||'[]');
console.log('ðŸ“‹ Columns config:',cols);

// HTML escape helper
function escapeHtml(text){
if(!text)return '';
var map={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
return String(text).replace(/[&<>"']/g,function(m){return map[m];});

var h='<div class="ajax-info" style="padding:10px;background:#e9ecef;margin-bottom:10px;">';
h+='<strong>Total Records:</strong> '+totalRecords+' | ';
var totalPages2=Math.ceil(totalRecords/limit);
h+='<strong>Page:</strong> '+currentPage+' of '+totalPages2+' | ';
var showStart=Math.min((currentPage-1)*limit+1,totalRecords);
var showEnd=Math.min(currentPage*limit,totalRecords);
h+='<strong>Showing:</strong> '+showStart+'-'+showEnd;
h+='</div>';

// Table with horizontal scroll wrapper
h+='<div class="ajax-table-wrapper">';
h+='<table class="ajax-table table-sm table-bordered">';
h+='<thead><tr>';
cols.forEach(function(col){h+='<th>'+col.label+'</th>';});
h+='<th>Actions</th></tr>';

// Filter row
h+='<tr class="ajax-filter-row">';
cols.forEach(function(col,idx){
// Support both 'name' and 'field' for column identifier
var fieldName = col.name || col.field;
h+='<th style="vertical-align:top;padding:8px;">';
if(col.sortable!==false){
// Get current filter value for this field (if exists)
var currentFilter=columnFilters[fieldName]||{};
var currentOperator=currentFilter.operator||'contains';
var currentValue=escapeHtml(currentFilter.value||'');

// Operator dropdown - restore selected value
h+='<select class="filter-operator" data-field="'+fieldName+'">';
h+='<option value="contains"'+(currentOperator==='contains'?' selected':'')+'>Contains</option>';
h+='<option value="equals"'+(currentOperator==='equals'?' selected':'')+'>Equals</option>';
h+='<option value="starts_with"'+(currentOperator==='starts_with'?' selected':'')+'>Starts with</option>';
h+='<option value="ends_with"'+(currentOperator==='ends_with'?' selected':'')+'>Ends with</option>';
h+='<option value="greater_than"'+(currentOperator==='greater_than'?' selected':'')+'>&gt;</option>';
h+='<option value="less_than"'+(currentOperator==='less_than'?' selected':'')+'>&lt;</option>';
if(col.type==='file'){
h+='<option value="file_exists"'+(currentOperator==='file_exists'?' selected':'')+'>File exists</option>';
h+='<option value="file_not_exists"'+(currentOperator==='file_not_exists'?' selected':'')+'>File missing</option>';
h+='</select>';
// Filter input - ALWAYS show with restored value
h+='<input type="text" class="filter-input" data-field="'+fieldName+'" value="'+currentValue+'" placeholder="Type to filter..." style="display:block;">';
}else{
h+='&nbsp;'; // Non-sortable columns
h+='</th>';
});
h+='<th style="background:#e9ecef;"></th>'; // No apply button anymore
h+='</tr></thead><tbody>';

if(data.data.length===0){
var totalCols=cols.length+1;
h+='<tr><td colspan="'+totalCols+'" class="text-center text-muted" style="padding:30px;">No records found</td></tr>';
}else{
data.data.forEach(function(row,idx){
if(idx===0){
console.log('ðŸ” First row data sample:',row);
console.log('ðŸ” Available fields:',Object.keys(row));
h+='<tr>';
cols.forEach(function(col){
// Support both 'name' and 'field' for column identifier
var fieldName = col.name || col.field;
var v=row[fieldName];
// Check if field is image/photo first (PRIORITY)
if((fieldName.indexOf('image')!==-1||fieldName.indexOf('photo')!==-1)&&v){
// Image/photo field - show thumbnail or broken icon
var fileExists=row[fieldName+'_exists'];
h+='<td style="text-align:center;">';
if(fileExists){
// File exists - show thumbnail
h+='<a href="/project_tmm/'+v+'" target="_blank" title="Click to view full image">';
h+='<img src="/project_tmm/'+v+'" style="max-width:50px;max-height:50px;object-fit:cover;border:1px solid #ddd;border-radius:3px;" />';
h+='</a>';
}else{
// File not exists - show broken image icon
h+='<span title="File not found: '+v+'" style="color:#dc3545;cursor:help;display:inline-block;">';
h+='<svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
h+='<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>';
h+='<circle cx="8.5" cy="8.5" r="1.5"></circle>';
h+='<polyline points="21 15 16 10 5 21"></polyline>';
h+='<line x1="3" y1="3" x2="21" y2="21" stroke="#dc3545" stroke-width="3"></line>';
h+='</svg>';
h+='<br><small style="font-size:10px;color:#999;">Not found</small>';
h+='</span>';
h+='</td>';
}else if(col.type==='file'&&v){
// Other file types (not image)
h+='<td><a href="/project_tmm/'+v+'" target="_blank" class="btn btn-xs btn-info">View File</a></td>';
}else if(col.type==='datetime'&&v){
var dateStr=new Date(v).toLocaleString();
h+='<td>'+dateStr+'</td>';
}else if(col.type==='date'&&v){
h+='<td>'+v+'</td>';
}else{
h+='<td>'+(v||'-')+'</td>';
});
var viewUrl='/project_tmm/'+c.dataset.controller.toLowerCase()+'/view/'+row.id;
h+='<td><a href="'+viewUrl+'" class="btn btn-xs btn-info">View</a></td>';
h+='</tr>';
});
h+='</tbody></table></div>';

// Pagination controls
var totalPages=Math.ceil(totalRecords/limit);
var prevPage=currentPage-1;
var nextPage=currentPage+1;
h+='<div class="ajax-pagination">';
h+='<div>';
h+='<button class="page-btn" data-page="1"'+(currentPage===1?' disabled':'')+'>First</button>';
h+='<button class="page-btn" data-page="'+prevPage+'"'+(currentPage===1?' disabled':'')+'>Previous</button>';
h+='</div>';
h+='<div>Page '+currentPage+' of '+totalPages+'</div>';
h+='<div>';
h+='<button class="page-btn" data-page="'+nextPage+'"'+(currentPage>=totalPages?' disabled':'')+'>Next</button>';
h+='<button class="page-btn" data-page="'+totalPages+'"'+(currentPage>=totalPages?' disabled':'')+'>Last</button>';
h+='</div></div>';

console.log('ðŸŽ¨ Generated HTML length:',h.length);
console.log('ðŸ“ HTML preview:',h.substring(0,200));

if(d){
console.log('âœ… Content div found, inserting HTML...');
d.innerHTML=h;
d.style.display='block';
console.log('âœ… HTML inserted and displayed');

// Auto-filter: Attach debounced event listeners
setTimeout(function(){
var filterTimeout;
function applyFilters(){
columnFilters={};
d.querySelectorAll('.filter-input').forEach(function(inp){
var field=inp.dataset.field;
var value=inp.value.trim();
var operator=d.querySelector('.filter-operator[data-field="'+field+'"]');
if(value||operator.value==='file_exists'||operator.value==='file_not_exists'){
columnFilters[field]={operator:operator?operator.value:'contains',value:value};
});
console.log('ðŸ” Auto-applying filters:',columnFilters);
load(1); // Reset to page 1 when filtering

// Debounce function (wait 800ms after user stops typing)
function debounceFilter(){
clearTimeout(filterTimeout);
filterTimeout=setTimeout(applyFilters,800);

// Attach to all filter inputs (keyup event)
d.querySelectorAll('.filter-input').forEach(function(inp){
inp.addEventListener('keyup',debounceFilter);
inp.addEventListener('paste',debounceFilter); // Also handle paste
});

// Attach to operator dropdowns (immediate apply on change)
d.querySelectorAll('.filter-operator').forEach(function(sel){
sel.addEventListener('change',function(){
// If there's text in the input, apply immediately
var field=sel.dataset.field;
var input=d.querySelector('.filter-input[data-field="'+field+'"]');
if(input&&(input.value.trim()||sel.value==='file_exists'||sel.value==='file_not_exists')){
applyFilters();
});
});
},100);
}else{
console.log('âŒ No content div found!');
}else{
console.log('âš ï¸ Data invalid - success:',data.success,'data exists:',!!data.data);
if(d){d.innerHTML='<div class="alert alert-warning">No data available</div>';d.style.display='block';}
}).catch(function(e){
console.error('âŒ Fetch Error:',e);
console.error('âŒ Error stack:',e.stack);
if(l)l.style.display='none';
if(d){d.innerHTML='<div class="alert alert-danger">Error loading data: '+e.message+'</div>';d.style.display='block';}
});

// Expose loadPage function
c.loadPage=function(p){load(p);};

// MutationObserver for tab visibility
if(tabPane){
console.log('ðŸ” Tab pane found for <?= h($tabId) ?>:', tabPane);
var observer=new MutationObserver(function(mutations){
mutations.forEach(function(mutation){
if(mutation.type==='attributes'){
var isVisible=tabPane.style.display!=='none'&&window.getComputedStyle(tabPane).display!=='none';
console.log('ðŸ” Tab visibility changed for <?= h($tabId) ?>:', isVisible, 'loaded:', loaded);
if(isVisible&&!loaded){
console.log('âœ… Loading data for <?= h($tabId) ?>');
load(1);
});
});
observer.observe(tabPane,{attributes:true,attributeFilter:['style','class']});
var initiallyVisible = window.getComputedStyle(tabPane).display!=='none';
console.log('ðŸ” Initial visibility for <?= h($tabId) ?>:', initiallyVisible);
if(initiallyVisible){setTimeout(function(){
console.log('âœ… Auto-loading <?= h($tabId) ?> (initially visible)');
load(1);
},100);}
}else{
console.error('âŒ Tab pane NOT found for <?= h($tabId) ?>');
})();
</script>

