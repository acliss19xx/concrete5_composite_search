<?php 
namespace Concrete\Package\CompositeSearch;

use Concrete\Core\Package\Package;
use BlockType;
use Concrete\Core\Block\BlockController;
use Loader;
use Route;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends Package
{

    protected $pkgHandle = 'composite_search'; //パッケージハンドル
    protected $appVersionRequired = '5.7.5.6'; //concrete5のバージョン
    protected $pkgVersion = '0.0.1'; //パッケージのバージョン
    protected $pkgAllowsFullContentSwap = false; //インストール時にコンテンツを上書きする

    public function getPackageDescription()
    {
        return t("It is an extension of the search block. The page attribute choice from block edit mode");  //パッケージの説明
    }

    public function getPackageName()
    {
        return t("composite search"); //パッケージ名
    }

    public function install()
    {
        $pkg = parent::install();
        BlockType::installBlockTypeFromPackage('composite_search', $pkg);

    }

    public function on_start()
    {
        Route::register('/ccm_composite_search/searchresult/',  'Concrete\Package\CompositeSearch\Controller\SearchBlockResult::getResult');
    }
}

