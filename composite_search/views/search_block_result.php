<?php defined('C5_EXECUTE') or die('Access Denied.');
if (count($results) == 0) {
    ?><h4 style="margin-top:32px"><?php echo t('There were no results found. Please try another keyword or phrase.')?></h4><?php
} else {
    $tt = Core::make('helper/text');
    foreach ($results as $r) {
        $currentPageBody = $this->controller->highlightedExtendedMarkup($r->getPageIndexContent(), $query);
        ?><div class="searchResult">
            <h3><a href="<?php echo $r->getCollectionLink()?>"><?php echo $r->getCollectionName()?></a></h3>
            <p><?php
                if ($r->getCollectionDescription()) {
                    echo $this->highlightedMarkup($tt->shortText($r->getCollectionDescription()), $query);
                    ?><br/><?php
                }
                echo $currentPageBody;
                ?> <br/><a href="<?php echo $r->getCollectionLink()?>" class="pageLink"><?php echo $this->controller->highlightedMarkup($r->getCollectionLink(), $query)?></a>
            </p>
        </div><?php
    } ?>
    <?php if ($showPagination): ?>
        <?php echo $pagination;?>
    <?php endif; ?>
<?php }