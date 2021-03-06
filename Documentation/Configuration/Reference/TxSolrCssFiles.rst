.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. raw:: latex

    \newpage

.. raw:: pdf

   PageBreak

.. _conf-tx-solr-cssFiles:

tx_solr.cssFiles
================

.. contents::
   :local:

Here you can configure what CSS files you want to use for different areas of the
extension. The section is a definition of key-value pairs where the key is the
name of a part of the extension and the value points to the CSS file to be used
for styling it.

To prevent loading of a file just set it empty like this:

|

.. code-block:: typoscript

   plugin.tx_solr.cssFiles.results =

or clear the setting like this:

|

.. code-block:: typoscript

   plugin.tx_solr.cssFiles.results >

To prevent the extension from loading any default CSS files simple clear the
whole cssFiles settings:

|

.. code-block:: typoscript

   plugin.tx_solr.cssFiles >


results
-------

:Type: String
:TS Path: plugin.tx_solr.cssFiles.results
:Default: EXT:solr/Resources/Css/PiResults/results.css
:Since: 2.0

Defines the stylesheet to be used for styling the search results page.

ui
--

:Type: String
:TS Path: plugin.tx_solr.cssFiles.ui
:Default: EXT:solr/Resources/Css/JQueryUi/jquery-ui.custom.css
:Since: 2.0

Defines the stylesheet to be used for styling the auto suggestions.
