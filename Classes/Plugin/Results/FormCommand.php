<?php

namespace ApacheSolrForTypo3\Solr\Plugin\Results;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2015 Ingo Renner <ingo@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use ApacheSolrForTypo3\Solr\Plugin\CommandPluginAware;
use ApacheSolrForTypo3\Solr\Plugin\CommandPluginBase;
use ApacheSolrForTypo3\Solr\Plugin\FormModifier;
use ApacheSolrForTypo3\Solr\Plugin\PluginCommand;
use ApacheSolrForTypo3\Solr\System\Configuration\TypoScriptConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Custom Section Get Typoscript setting --- start ---
 */
/**
 * Custom Section Typoscript setting ---  end  ---
 */

/**
 * form command class to render the "simple" search form
 *
 * @author Ingo Renner <ingo@typo3.org>
 */
class FormCommand implements PluginCommand {

  /**
   *
   * @var ContentObjectRenderer
   */
  protected $cObj;

  /**
   * Parent plugin
   *
   * @var CommandPluginBase
   */
  protected $parentPlugin;

  /**
   * Configuration
   *
   * @var TypoScriptConfiguration
   */
  protected $configuration;

  /**
   * Constructor for class ApacheSolrForTypo3\Solr\Plugin\Results\FormCommand
   *
   * @param CommandPluginBase $parentPlugin parent plugin
   */
  public function __construct( CommandPluginBase $parentPlugin ) {
    $this->cObj = GeneralUtility::makeInstance( ContentObjectRenderer::class );

    $this->parentPlugin  = $parentPlugin;
    $this->configuration = $parentPlugin->typoScriptConfiguration;

    /**
     * Custom section --- start ---
     */
    $this->search = $parentPlugin->getSearchResultSetService()->getSearch();
    /**
     * Custom section ---  end  ---
     */
  }

  /**
   * Provides the values for the markers in the simple form template
   *
   * @return array An array containing values for markers in the simple form template
   * @throws \InvalidArgumentException if an registered form modifier fails to implement the required interface ApacheSolrForTypo3\Solr\Plugin\FormModifier
   */
  public function execute() {
    $url = $this->cObj->getTypoLink_URL( $this->parentPlugin->typoScriptConfiguration->getSearchTargetPage() );

    /**
     *  Custom section --- start ---
     */
    $solrCustomConfigResultSingle = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr_pi_results.']['settings.']['solrResSingleRecord'];
    $solrCustomConfigResultMulti  = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_solr_pi_results.']['settings.']['solrResMoreRecords'];

    $solrCustomResultNum = (int) $this->search->getNumberOfResults();

    if ($solrCustomResultNum >1) {
      $solrCustomResultText = $solrCustomResultNum . ' ' . $solrCustomConfigResultMulti;
    }else{
      $solrCustomResultText = $solrCustomResultNum . ' ' . $solrCustomConfigResultSingle;
    }

//    $foundResultsNumberCs = $this->search->getNumberOfResults() . ' results found';
    $foundResultsNumberCs = $solrCustomResultText;
    /**
     *  Custom section ---  end  ---
     */

    $marker = [
      'action'                    => htmlspecialchars( $url ),
      'action_id'                 => intval( $this->parentPlugin->typoScriptConfiguration->getSearchTargetPage() ),
      'action_language'           => intval( $GLOBALS['TSFE']->sys_page->sys_language_uid ),
      'action_language_parameter' => 'L',
      'accept-charset'            => $GLOBALS['TSFE']->metaCharset,
      'q'                         => $this->parentPlugin->getCleanUserQuery(),
      /**
       *  Custom section --- start ---
       */
      'found_results_number_str'  => $foundResultsNumberCs
      /**
       *  Custom section ---  end  ---
       */
    ];

    // hook to modify the search form
    if ( is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['modifySearchForm'] ) ) {
      foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['modifySearchForm'] as $classReference ) {
        $formModifier = GeneralUtility::getUserObj( $classReference );

        if ( $formModifier instanceof FormModifier ) {
          if ( $formModifier instanceof CommandPluginAware ) {
            $formModifier->setParentPlugin( $this->parentPlugin );
          }

          $marker = $formModifier->modifyForm( $marker,
            $this->parentPlugin->getTemplate() );
        } else {
          throw new \InvalidArgumentException(
            'Form modifier "' . $classReference . '" must implement the ApacheSolrForTypo3\Solr\Plugin\FormModifier interface.',
            1262864703
          );
        }
      }
    }

    return $marker;
  }
}
