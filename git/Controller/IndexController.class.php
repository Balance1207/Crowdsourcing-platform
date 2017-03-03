<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */
class IndexController extends ArController
{
    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function indexAction()
    {
        $repo = new \Lib\Ext\PHPGit\PHPGit_Repository('e:/web/quickoa');
$branches = $repo->getBranches();
var_dump($branches);

    }

}