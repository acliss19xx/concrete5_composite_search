<?php 
namespace Concrete\Package\CompositeSearch\Controller;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\Request;
use Database;
use CollectionAttributeKey;
use Concrete\Core\Page\PageList;
use Page;
use Core;

class SearchBlockResult extends Controller {
    protected $viewPath = '/search_block_result';
    public function getResult() {
        $q = $_REQUEST['query'];
        // i have NO idea why we added this in rev 2000. I think I was being stupid. - andrew
        // $_q = trim(preg_replace('/[^A-Za-z0-9\s\']/i', ' ', $_REQUEST['query']));
        $_q = $q;
        
        $ipl = new PageList();
        $aksearch = false;
        if (is_array($_REQUEST['akID'])) {
            foreach ($_REQUEST['akID'] as $akID => $req) {
                $fak = CollectionAttributeKey::getByID($akID);
                if (is_object($fak)) {
                    $type = $fak->getAttributeType();
                    $cnt = $type->getController();
                    $cnt->setAttributeKey($fak);
                    $cnt->searchForm($ipl);
                    $aksearch = true;
                }
            }
        }
        if (isset($_REQUEST['month']) && isset($_REQUEST['year'])) {
            $year = @intval($_REQUEST['year']);
            $month = abs(@intval($_REQUEST['month']));
            if (strlen(abs($year)) < 4) {
                $year = (($year < 0) ? '-' : '') . str_pad($year, 4, '0', STR_PAD_LEFT);
            }
            if ($month < 12) {
                $month = str_pad($month, 2, '0', STR_PAD_LEFT);
            }
            $daysInMonth = date('t', strtotime("$year-$month-01"));
            $dh = Core::make('helper/date');
            // @var $dh \Concrete\Core\Localization\Service\Date 
            $start = $dh->toDB("$year-$month-01 00:00:00", 'user');
            $end = $dh->toDB("$year-$month-$daysInMonth 23:59:59", 'user');
            $ipl->filterByPublicDate($start, '>=');
            $ipl->filterByPublicDate($end, '<=');
            $aksearch = true;
        }

        if (empty($_REQUEST['query']) && $aksearch == false) {
            return false;
        }

        if (isset($_REQUEST['query'])) {
            $ipl->filterByKeywords($_q);
        }

        if (is_array($_REQUEST['search_paths'])) {
            foreach ($_REQUEST['search_paths'] as $path) {
                if (!strlen($path)) {
                    continue;
                }
                $ipl->filterByPath($path);
            }
        } elseif ($this->baseSearchPath != '') {
            $ipl->filterByPath($this->baseSearchPath);
        }

//        $results = $ipl->getResults();
        $ipl->setItemsPerPage(10);
        $pagination = $ipl->getPagination();
        $results = $pagination->getCurrentPageResults();
        if ($pagination->getTotalPages() > 1) {
            $showPagination = true;
            $pagination = $pagination->renderDefaultView();
        }
        $this->set('results',$results);
        $this->set('pagination',$pagination);

    }

    public function highlightedExtendedMarkup($fulltext, $highlight)
    {
        $text = @preg_replace("#\n|\r#", ' ', $fulltext);

        $matches = array();
        $highlight = str_replace(array('"', "'", "&quot;"), '', $highlight); // strip the quotes as they mess the regex

        if (!$highlight) {
            $text = Core::make('helper/text')->shorten($fulltext, 180);
            if (strlen($fulltext) > 180) {
                $text .= '&hellip;<wbr>';
            }

            return $text;
        }

        $regex = '([[:alnum:]|\'|\.|_|\s]{0,45})'. preg_quote($highlight, '#') .'([[:alnum:]|\.|_|\s]{0,45})';
        preg_match_all("#$regex#ui", $text, $matches);

        if (!empty($matches[0])) {
            $body_length = 0;
            $body_string = array();
            foreach ($matches[0] as $line) {
                $body_length += strlen($line);

                $r = $this->highlightedMarkup($line, $highlight);
                if ($r) {
                    $body_string[] = $r;
                }
                if ($body_length > 150) {
                    break;
                }
            }
            if (!empty($body_string)) {
                return @implode("&hellip;<wbr>", $body_string);
            }
        }
    }

    public function highlightedMarkup($fulltext, $highlight)
    {
        if (!$highlight) {
            return $fulltext;
        }

        $this->hText = $fulltext;
        $this->hHighlight = $highlight;
        $this->hText = @preg_replace('#' . preg_quote($this->hHighlight, '#') . '#ui', '<span style="background-color:'. $this->hColor .';">$0</span>', $this->hText);

        return $this->hText;
    }    
    
}
