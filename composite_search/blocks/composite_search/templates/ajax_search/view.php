<?php defined('C5_EXECUTE') or die('Access Denied.');

if (isset($error)) {
    ?><?php echo $error?><br/><br/><?php
}

if (!isset($query) || !is_string($query)) {
    $query = '';
}

?><form action="<?php echo $view->url($resultTargetURL)?>" method="get" id="composite-search-block<?php echo $bID; ?>" class="ccm-search-block-form"><?php
    if (isset($title) && ($title !== '')) {
        ?><h3><?php echo h($title)?></h3><?php
    }
    if ($query === '') {
        ?><input name="search_paths[]" type="hidden" value="<?php echo htmlentities($baseSearchPath, ENT_COMPAT, APP_CHARSET) ?>" /><?php
    } elseif (isset($_REQUEST['search_paths']) && is_array($_REQUEST['search_paths'])) {
        foreach ($_REQUEST['search_paths'] as $search_path) {
            ?><input name="search_paths[]" type="hidden" value="<?php echo htmlentities($search_path, ENT_COMPAT, APP_CHARSET) ?>" /><?php
        }
    }
    ?><input name="query" type="text" value="<?php echo htmlentities($query, ENT_COMPAT, APP_CHARSET)?>" class="ccm-search-block-text" />

    <?php
    $atks = json_decode($attributeKeys);
    use Concrete\Core\Attribute\Key\CollectionKey as CollectionAttributeKey;
    foreach($atks as $atk){
        $ak = CollectionAttributeKey::getByID($atk);
        if(is_object($ak)){ ?>
            <h4><?php echo $ak->getAttributeKeyDisPlayName() . '------' .  $ak->getAttributeKeyType()->getAttributeTypeID();?></h4>
            <div>
                <?php 
                $ak->render('search'); ?>
            </div>
        <?php }
    }?>
    
    <div id="search-results<?php echo $bID; ?>"></div>
</form>
<script type="text/javascript">

    $('#composite-search-block<?php echo $bID; ?>').change( function() {
        $.ajax({
            type: 'GET',
            url: '<?php echo URL::to('/ccm_composite_search/searchresult/');?>' ,
            data: $('#composite-search-block<?php echo $bID; ?>').serialize(),
            success:function(r){
                $("#search-results<?php echo $bID; ?>").html(r);
            }
        })
    });    
</script>
<?php