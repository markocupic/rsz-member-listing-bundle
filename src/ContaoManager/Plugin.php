<?php

declare(strict_types=1);

/*
 * This file is part of RSZ Member Listing Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/rsz-member-listing-bundle
 */

namespace Markocupic\RszMemberListingBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Markocupic\RszBenutzerverwaltungBundle\MarkocupicRszBenutzerverwaltungBundle;
use Markocupic\RszMemberListingBundle\MarkocupicRszMemberListingBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(MarkocupicRszMemberListingBundle::class)
                ->setLoadAfter([
                    MarkocupicRszBenutzerverwaltungBundle::class,
                    ContaoCoreBundle::class,
                ]),
        ];
    }

}
