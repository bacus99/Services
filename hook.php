<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 Services plugin for GLPI
 Copyright (C) 2003-2011 by the Services Development Team.

 https://forge.indepnet.net/projects/services
 -------------------------------------------------------------------------

 LICENSE
      
 This file is part of Services.

 Services is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Services is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Services. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

function plugin_services_install() {
   global $DB;

   include_once (GLPI_ROOT."/plugins/services/inc/profile.class.php");

   $update = false;
   if (!TableExists("glpi_application")
       && !TableExists("glpi_plugin_appweb")
       && !TableExists("glpi_plugin_services_services")) {

      $DB->runFile(GLPI_ROOT ."/plugins/services/sql/empty-1.0.0.sql");

   } else {
      
      if (TableExists("glpi_application") && !TableExists("glpi_plugin_appweb")) {
         $update = true;
         $DB->runFile(GLPI_ROOT ."/plugins/services/sql/update-1.1.sql");
      }

      //from 1.1 version
      if (TableExists("glpi_plugin_appweb") && !FieldExists("glpi_plugin_appweb","location")) {
         $update = true;
         $DB->runFile(GLPI_ROOT ."/plugins/services/sql/update-1.3.sql");
      }

      //from 1.3 version
      if (TableExists("glpi_plugin_appweb") && !FieldExists("glpi_plugin_appweb","recursive")) {
         $update = true;
         $DB->runFile(GLPI_ROOT ."/plugins/services/sql/update-1.4.sql");
      }

      if (TableExists("glpi_plugin_appweb_profiles")
          && FieldExists("glpi_plugin_appweb_profiles","interface")) {
         $update = true;
         $DB->runFile(GLPI_ROOT ."/plugins/services/sql/update-1.5.0.sql");
      }

      if (TableExists("glpi_plugin_appweb")
              && !FieldExists("glpi_plugin_appweb","helpdesk_visible")) {
         $update = true;
         $DB->runFile(GLPI_ROOT ."/plugins/services/sql/update-1.5.1.sql");
      }

      if (!TableExists("glpi_plugin_services_services")) {
         $update = true;
         $DB->runFile(GLPI_ROOT ."/plugins/services/sql/update-1.6.0.sql");
      }
      
      //from 1.6 version
      if (TableExists("glpi_plugin_services_services") 
         && !FieldExists("glpi_plugin_services_services","users_id_tech")) {
         $DB->runFile(GLPI_ROOT ."/plugins/services/sql/update-1.8.0.sql");
      }
   }
   
   if (TableExists("glpi_plugin_services_profiles")) {
   
      $notepad_tables = array('glpi_plugin_services_services');

      foreach ($notepad_tables as $t) {
         // Migrate data
         if (FieldExists($t, 'notepad')) {
            $query = "SELECT id, notepad
                      FROM `$t`
                      WHERE notepad IS NOT NULL
                            AND notepad <>'';";
            foreach ($DB->request($query) as $data) {
               $iq = "INSERT INTO `glpi_notepads`
                             (`itemtype`, `items_id`, `content`, `date`, `date_mod`)
                      VALUES ('".getItemTypeForTable($t)."', '".$data['id']."',
                              '".addslashes($data['notepad'])."', NOW(), NOW())";
               $DB->queryOrDie($iq, "0.85 migrate notepad data");
            }
            $query = "ALTER TABLE `glpi_plugin_services_services` DROP COLUMN `notepad`;";
            $DB->query($query);
         }
      }
   }

   if ($update) {
      $query_= "SELECT *
                FROM `glpi_plugin_services_profiles` ";
      $result_=$DB->query($query_);
      if ($DB->numrows($result_)>0) {

         while ($data=$DB->fetch_array($result_)) {
            $query = "UPDATE `glpi_plugin_services_profiles`
                      SET `profiles_id` = '".$data["id"]."'
                      WHERE `id` = '".$data["id"]."';";
            $result = $DB->query($query);
         }
      }

      $query = "ALTER TABLE `glpi_plugin_services_profiles`
               DROP `name` ;";
      $result = $DB->query($query);

      Plugin::migrateItemType(array(1300 => 'PluginServicesService'),
                              array("glpi_bookmarks", "glpi_bookmarks_users",
                                    "glpi_displaypreferences", "glpi_documents_items",
                                    "glpi_infocoms", "glpi_logs", "glpi_items_tickets"),
                              array("glpi_plugin_services_services_items"));

      Plugin::migrateItemType(array(1200 => "PluginAppliancesAppliance"),
                              array("glpi_plugin_services_services_items"));
   }

   PluginServicesProfile::initProfile();
   PluginServicesProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);
   $migration = new Migration("2.0.0");
   $migration->dropTable('glpi_plugin_services_profiles');
   
   return true;
}


function plugin_services_uninstall() {
   global $DB;
   
   include_once (GLPI_ROOT."/plugins/services/inc/profile.class.php");
   include_once (GLPI_ROOT."/plugins/services/inc/menu.class.php");
   
   $tables = array("glpi_plugin_services_services",
                   "glpi_plugin_services_servicetypes",
                   "glpi_plugin_services_serviceservertypes",
				   "glpi_plugin_services_servicesupports",
				   "glpi_plugin_services_servicetiers",
                   //"glpi_plugin_services_servicetechnics",
                   "glpi_plugin_services_services_items");

   foreach($tables as $table) {
      $DB->query("DROP TABLE IF EXISTS `$table`;");
   }

   //old versions
   $tables = array("glpi_plugin_appweb",
                   "glpi_dropdown_plugin_appweb_type",
                   "glpi_dropdown_plugin_appweb_server_type",
                   "glpi_dropdown_plugin_appweb_technic",
                   "glpi_plugin_appweb_device",
                   "glpi_plugin_appweb_profiles",
                   "glpi_plugin_services_profiles");

   foreach($tables as $table) {
      $DB->query("DROP TABLE IF EXISTS `$table`;");
   }

   $tables_glpi = array("glpi_displaypreferences",
                        "glpi_documents_items",
                        "glpi_bookmarks",
                        "glpi_logs",
                        "glpi_items_tickets",
                        "glpi_notepads",
                        "glpi_dropdowntranslations");

   foreach($tables_glpi as $table_glpi) {
      $DB->query("DELETE
                  FROM `$table_glpi`
                  WHERE `itemtype` LIKE 'PluginServices%'");
   }

   if (class_exists('PluginDatainjectionModel')) {
      PluginDatainjectionModel::clean(array('itemtype' => 'PluginServicesService'));
   }
   
   //Delete rights associated with the plugin
   $profileRight = new ProfileRight();
   foreach (PluginServicesProfile::getAllRights() as $right) {
      $profileRight->deleteByCriteria(array('name' => $right['field']));
   }
   PluginServicesMenu::removeRightsFromSession();
   PluginServicesProfile::removeRightsFromSession();

   return true;
}


// Define dropdown relations
function plugin_services_getDatabaseRelations() {

   $plugin = new Plugin();

   if ($plugin->isActivated("services")) {
      return array("glpi_plugin_services_servicetypes"
                        => array("glpi_plugin_services_services"
                                    => "plugin_services_servicetypes_id"),
                   "glpi_plugin_services_serviceservertypes"
                        => array("glpi_plugin_services_services"
                                    =>"plugin_services_serviceservertypes_id"),
                   "glpi_plugin_services_servicetechnics"
                        => array("glpi_plugin_services_services"
                                    =>"plugin_services_servicetechnics_id"),
                   "glpi_users"
                        => array("glpi_plugin_services_services" => "users_id_tech"),
                   "glpi_groups"
                        => array("glpi_plugin_services_services" => "groups_id_tech"),
                   "glpi_suppliers"
                        => array("glpi_plugin_services_services" => "suppliers_id"),
                   "glpi_manufacturers"
                        => array("glpi_plugin_services_services" => "manufacturers_id"),
                   "glpi_locations"
                        => array("glpi_plugin_services_services" => "locations_id"),
                   "glpi_plugin_services_services"
                        => array("glpi_plugin_services_services_items"
                                    => "plugin_services_services_id"),
                   "glpi_entities"
                        => array("glpi_plugin_services_services"     => "entities_id",
                                 "glpi_plugin_services_servicetypes" => "entities_id"));
   }
   return array();
}


// Define Dropdown tables to be manage in GLPI :
function plugin_services_getDropdown() {

   $plugin = new Plugin();

   if ($plugin->isActivated("services")) {
      return array(	'PluginServicesServiceBusPriority'
                        => PluginServicesServiceBusPriority::getTypeName(2),
					'PluginServicesServiceBusRisk'
                        => PluginServicesServiceBusRisk::getTypeName(2),
				    'PluginServicesServiceSupport'
                        => PluginServicesServiceSupport::getTypeName(2),
                    'PluginServicesServiceRto'
                        => PluginServicesServiceRto::getTypeName(2),
                    'PluginServicesServiceRpo'
					=> PluginServicesServiceRpo::getTypeName(2),
                    'PluginServicesServiceType'
                        => PluginServicesServiceType::getTypeName(2));
   }
   return array();
}


function plugin_services_AssignToTicket($types) {

   if (Session::haveRight("plugin_services_open_ticket", "1")) {
      $types['PluginServicesService'] = PluginServicesService::getTypeName(2);
   }
   return $types;
}


////// SEARCH FUNCTIONS ///////() {

function plugin_services_getAddSearchOptions($itemtype) {

   $sopt = array();

   if (in_array($itemtype, PluginServicesService::getTypes(true))) {
      
      if (Session::haveRight("plugin_services", READ)) {
         $sopt[1410]['table']          = 'glpi_plugin_services_services';
         $sopt[1410]['field']          = 'name';
         $sopt[1410]['name']           = PluginServicesService::getTypeName(2)." - ".
                                         __('Name');
         $sopt[1410]['forcegroupby']   = true;
         $sopt[1410]['datatype']       = 'itemlink';
         $sopt[1410]['massiveaction']  = false;
         $sopt[1410]['itemlink_type']  = 'PluginServicesService';
         $sopt[1410]['joinparams']     = array('beforejoin'
                                                   => array('table'      => 'glpi_plugin_services_services_items',
                                                            'joinparams' => array('jointype' => 'itemtype_item')));
                                                            
         $sopt[1411]['table']          = 'glpi_plugin_services_servicetypes';
         $sopt[1411]['field']          = 'name';
         $sopt[1411]['name']           = PluginServicesService::getTypeName(2)." - ".
                                         PluginServicesServiceType::getTypeName(1);
         $sopt[1411]['forcegroupby']   = true;
         $sopt[1411]['datatype']       = 'dropdown';
         $sopt[1411]['massiveaction']  = false;
         $sopt[1411]['joinparams']     = array('beforejoin' => array(
                                                      array('table'      => 'glpi_plugin_services_services',
                                                            'joinparams' => $sopt[1410]['joinparams'])));
      }
   }

   return $sopt;
}

//display custom fields in the search
function plugin_services_giveItem($type, $ID, $data, $num) {
   global $CFG_GLPI, $DB;

   $searchopt  = &Search::getOptions($type);
   $table      = $searchopt[$ID]["table"];
   $field      = $searchopt[$ID]["field"];

   switch ($table.'.'.$field) {
      //display associated items with services
      case "glpi_plugin_services_services_items.items_id" :
         $query_device     = "SELECT DISTINCT `itemtype`
                              FROM `glpi_plugin_services_services_items`
                              WHERE `plugin_services_services_id` = '".$data['id']."'
                              ORDER BY `itemtype`";
         $result_device    = $DB->query($query_device);
         $number_device    = $DB->numrows($result_device);
         $out              = '';
         $services  = $data['id'];
         if ($number_device > 0) {
            for ($i=0 ; $i < $number_device ; $i++) {
               $column   = "name";
               $itemtype = $DB->result($result_device, $i, "itemtype");
               if (!class_exists($itemtype)) {
                  continue;
               }
               $item = new $itemtype();
               if ($item->canView()) {
                  $table_item = getTableForItemType($itemtype);

                  if ($itemtype != 'Entity') {
                     $query = "SELECT `".$table_item."`.*,
                                      `glpi_plugin_services_services_items`.`id` AS table_items_id,
                                      `glpi_entities`.`id` AS entity
                               FROM `glpi_plugin_services_services_items`,
                                    `".$table_item."`
                               LEFT JOIN `glpi_entities`
                                 ON (`glpi_entities`.`id` = `".$table_item."`.`entities_id`)
                               WHERE `".$table_item."`.`id` = `glpi_plugin_services_services_items`.`items_id`
                                     AND `glpi_plugin_services_services_items`.`itemtype` = '$itemtype'
                                     AND `glpi_plugin_services_services_items`.`plugin_services_services_id` = '".$services."' "
                                   . getEntitiesRestrictRequest(" AND ", $table_item, '', '',
                                                                $item->maybeRecursive());

                     if ($item->maybeTemplate()) {
                        $query .= " AND ".$table_item.".is_template = '0'";
                     }
                     $query .= " ORDER BY `glpi_entities`.`completename`,
                                          `".$table_item."`.`$column` ";

                  } else {
                     $query = "SELECT `".$table_item."`.*,
                                      `glpi_plugin_services_services_items`.`id` AS table_items_id,
                                      `glpi_entities`.`id` AS entity
                               FROM `glpi_plugin_services_services_items`, `".$table_item."`
                               WHERE `".$table_item."`.`id` = `glpi_plugin_services_services_items`.`items_id`
                                     AND `glpi_plugin_services_services_items`.`itemtype` = '$itemtype'
                                     AND `glpi_plugin_services_services_items`.`plugin_services_services_id` = '".$services."' "
                                   . getEntitiesRestrictRequest(" AND ", $table_item, '', '',
                                                                $item->maybeRecursive());

                     if ($item->maybeTemplate()) {
                        $query .= " AND ".$table_item.".is_template = '0'";
                     }
                     $query .= " ORDER BY `glpi_entities`.`completename`,
                                          `".$table_item."`.`$column` ";
                  }
               
                  if ($result_linked=$DB->query($query)) {
                     if ($DB->numrows($result_linked)) {
                        $item = new $itemtype();
                        while ($datal=$DB->fetch_assoc($result_linked)) {
                           if ($item->getFromDB($datal['id'])) {
                              $out .= $item->getTypeName()." - ".$item->getLink()."<br>";
                           }
                        }
                     } else {
                        $out .= ' ';
                     }
                  } else {
                     $out .= ' ';
                  }
               } else {
                  $out .= ' ';
               }
            }
         }
         return $out;
   }
   return "";
}


////// SPECIFIC MODIF MASSIVE FUNCTIONS ///////

function plugin_services_MassiveActions($type) {

   if (in_array($type,PluginServicesService::getTypes(true))) {
      return array('PluginServicesService'.MassiveAction::CLASS_ACTION_SEPARATOR.'plugin_services_add_item' =>
                                                              __('Associate a ITIL Service', 'ITIL Services'));
   }
   return array();
}

/*
function plugin_services_MassiveActionsDisplay($options=array()) {

   $web = new PluginServicesService();

   if (in_array($options['itemtype'], PluginServicesService::getTypes(true))) {
      $web->dropdownWebApplications("plugin_services_services_id");
      echo "<input type=\"submit\" name=\"massiveaction\" class=\"submit\" value=\"" . _sx('button','Post') . "\" >";
   }
   return "";
}


function plugin_services_MassiveActionsProcess($data) {
   
   $web_item = new PluginServicesService_Item();
   
   $res = array('ok' => 0,
               'ko' => 0,
               'noright' => 0);

   switch ($data['action']) {
      case "plugin_services_add_item":     
         foreach ($data["item"] as $key => $val) {
            if ($val == 1) {
               $input = array('plugin_services_services_id' => $data['plugin_services_services_id'],
                        'items_id'      => $key,
                        'itemtype'      => $data['itemtype']);
               if ($web_item->can(-1,'w',$input)) {
                  if ($web_item->add($input)){
                     $res['ok']++;
                  } else {
                     $res['ko']++;
                  }
               } else {
                  $res['noright']++;
               }
            }
         }
         break;
   }
   return $res;
}
*/
function plugin_services_postinit() {
   global $CFG_GLPI, $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['item_purge']['services'] = array();

   foreach (PluginServicesService::getTypes(true) as $type) {

      $PLUGIN_HOOKS['item_purge']['services'][$type]
         = array('PluginServicesService_Item','cleanForItem');

      CommonGLPI::registerStandardTab($type, 'PluginServicesService_Item');
   }
}

function plugin_datainjection_populate_services() {
   global $INJECTABLE_TYPES;

   $INJECTABLE_TYPES['PluginServicesServiceInjection'] = 'services';
}

?>