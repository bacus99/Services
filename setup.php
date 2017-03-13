<?php
/*
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2011 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Christian Bernard
// Purpose of file: Manage services and products
// ----------------------------------------------------------------------

// Init the hooks of the plugins -Needed
function plugin_init_services() {
   global $PLUGIN_HOOKS, $CFG_GLPI;
 
   // CSRF compliance : All actions must be done via POST and forms closed by Html::closeForm(); 
   $PLUGIN_HOOKS['csrf_compliant']['services'] = true;
   //load changeprofile function
   $PLUGIN_HOOKS['change_profile']['services']   = array('PluginServicesProfile',
                                                                'initProfile');
   $PLUGIN_HOOKS['assign_to_ticket']['services'] = true;

   if (class_exists('PluginServicesService_Item')) { // only if plugin activated
      $PLUGIN_HOOKS['plugin_datainjection_populate']['services']
                                       = 'plugin_datainjection_populate_services';
   }

   // Params : plugin name - string type - number - class - table - form page
   Plugin::registerClass('PluginServicesService',
                         array('linkgroup_tech_types'         => true,
                               'linkuser_tech_types'          => true,
                               'document_types'          => true,
                               'contract_types'          => true,
                               'ticket_types'            => true,
                               'helpdesk_visible_types'  => true,
                               'addtabon' => 'Supplier'));
   
   Plugin::registerClass('PluginServicesProfile', array('addtabon' => array('Profile')));
   
   if (class_exists('PluginAccountsAccount')) {
      PluginAccountsAccount::registerType('PluginServicesService');
   }
   
   if (class_exists('PluginCertificatesCertificate')) {
      PluginCertificatesCertificate::registerType('PluginServicesService');
   }
   
   //if glpi is loaded
   if (Session::getLoginUserID()) {

      //if environment plugin is installed
      $plugin = new Plugin();
      if (!$plugin->isActivated('environment') 
         && Session::haveRight("plugin_services", READ)) {

         $PLUGIN_HOOKS['menu_toadd']['services'] = array('plugins'   => 'PluginServicesMenu');
      }
      
      if (Session::haveRight("plugin_services", UPDATE)) {
         $PLUGIN_HOOKS['use_massive_action']['services']=1;
      }

      if (Session::haveRight("plugin_services", READ)
          || Session::haveRight("config",UPDATE)) {
       }

      // Import from Data_Injection plugin
//      $PLUGIN_HOOKS['migratetypes']['services']
 //                                   = 'plugin_datainjection_migratetypes_services';
      $PLUGIN_HOOKS['plugin_pdf']['PluginServicesService']
                                 = 'PluginServicesServicePDF';
   }
   
   // End init, when all types are registered
      $PLUGIN_HOOKS['post_init']['services'] = 'plugin_services_postinit';
}


// Get the name and the version of the plugin - Needed
function plugin_version_services() {

   return array('name'          => _n('ITIL Service' , 'ITIL Services' ,2, 'services'),
                'version'        => '1.0.6',
                'license'        => 'GPLv2+',
                'author'  => "Christian Bernard, based on WebApplications plugin",
                'minGlpiVersion' => '9.1');
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_services_check_prerequisites() {

   if (version_compare(GLPI_VERSION,'9.1','lt') || version_compare(GLPI_VERSION,'9.2','ge')) {
      _e('This plugin requires GLPI >= 9.1', 'services');
      return false;
   }
   return true;
}


// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function plugin_services_check_config() {
   return true;
}

?>
