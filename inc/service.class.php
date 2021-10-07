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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginServicesService
 */
class PluginServicesService extends CommonDBTM {

   public    $dohistory  = true;
   static    $rightname  = "plugin_services";
   protected $usenotepad = true;

   static $types = ['Computer', 'Monitor', 'NetworkEquipment', 'Peripheral', 'Phone',
                         'Printer', 'Software', 'Entity', 'SoftwareLicense', 'PluginServicesService','Certificate'];
   static $tags  = '[SERVICE_URL]';

   /**
    * @param int $nb
    *
    * @return translated
    */
   static function getTypeName($nb = 0) {

      return _n('TC Service', 'TC Services', $nb, 'services');
   }

   
   //clean if services are deleted
   /**
    *
    */
   function cleanDBonPurge() {

      $temp = new PluginServicesService_Item();
      $temp->deleteByCriteria(['plugin_services_services_id' => $this->fields['id']]);
   }

   /**
    * @param CommonGLPI $item
    * @param int        $withtemplate
    *
    * @return array|string|translated
    */
   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if ($item->getType() == 'Supplier') {
         if ($_SESSION['glpishow_count_on_tabs']) {
            return self::createTabEntry(self::getTypeName(2), self::countForItem($item));
         }
         return self::getTypeName(2);
      }
      return '';
   }


   /**
    * @param CommonGLPI $item
    * @param int        $tabnum
    * @param int        $withtemplate
    *
    * @return bool
    */
   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {

      if ($item->getType() == 'Supplier') {
         PluginServicesService_Item::showForSupplier($item);
      }
      return true;
   }

   
   /**
    * @param CommonDBTM $item
    *
    * @return int
    */
   static function countForItem(CommonDBTM $item) {
      $dbu = new DbUtils();
      return $dbu->countElementsInTable('glpi_plugin_services_services',
                                        ["suppliers_id" => $item->getID()]);
   }

   
/*    function getSearchOptions() {
      global $LANG;

      $tab                       = array();
    
      $tab['common']             = self::getTypeName(2);

	  // Name
      $tab[1]['table']           = $this->getTable();
      $tab[1]['field']           = 'name';
      $tab[1]['name']            = __('Product Name');
      $tab[1]['datatype']        = 'itemlink';
      $tab[1]['itemlink_type']   = $this->getType();

      // Application Specialist
	  $tab[5]['table']          = 'glpi_users';
      $tab[5]['field']          = 'name';
      $tab[5]['linkfield']      = 'users_id_app';
      $tab[5]['name']           = __('Application Specialist');
      $tab[5]['datatype']       = 'dropdown';
	  $tab[5]['right']           = 'interface';
	  
      // Secondary Application Specialist
	  $tab[10]['table']          = 'glpi_users';
      $tab[10]['field']          = 'name';
      $tab[10]['linkfield']      = 'users_id_secapp';
      $tab[10]['name']           = __('Secondary Application Specialist');
      $tab[10]['datatype']       = 'dropdown';
	  $tab[10]['right']           = 'interface';	  

      // Associated item
	  $tab[15]['table']          = 'glpi_plugin_services_services_items';
      $tab[15]['field']          = 'items_id';
      $tab[15]['nosearch']       = true;
      $tab[15]['massiveaction']  = false;
      $tab[15]['name']           = _n('Associated item' , 'Associated items', 2);
      $tab[15]['forcegroupby']   = true;
      $tab[15]['joinparams']     = array('jointype' => 'child');

      // Business owner
	  $tab[20]['table']           = 'glpi_users';
      $tab[20]['field']           = 'name';
      $tab[20]['linkfield']       = 'users_id_bus';
      $tab[20]['name']            = __('Business owner');
      $tab[20]['datatype']        = 'dropdown';
      $tab[20]['right']           = 'interface';

	  // Business priorities
	  $tab[25]['table']           = 'glpi_plugin_services_servicebuspriorities';
      $tab[25]['field']           = 'name';
      $tab[25]['name']            = PluginServicesServicebuspriority::getTypeName(1);
      $tab[25]['datatype']        = 'dropdown';

	  // Business risks
	  $tab[30]['table']           = 'glpi_plugin_services_servicebusrisks';
      $tab[30]['field']           = 'name';
      $tab[30]['name']            = PluginServicesServiceBusRisk::getTypeName(1);
      $tab[30]['datatype']        = 'dropdown';
	  
	  // Categories
	  $tab[35]['table']          = 'glpi_itilcategories';
      $tab[35]['field']          = 'completename';
      $tab[35]['name']           = __('Category');
      $tab[35]['datatype']       = 'dropdown';
	  
	  // Comments
      $tab[40]['table']           = $this->getTable();
      $tab[40]['field']           = 'comment';
      $tab[40]['name']            = __('Comments');
      $tab[40]['datatype']        = 'text';

	  // Dashboard URL
      $tab[45]['table']           = $this->getTable();
      $tab[45]['field']           = 'Address';
      $tab[45]['name']            = __('Dashboard URL');
      $tab[45]['datatype']        = 'weblink';
	  
      // Distribution list
	  $tab[50]['table']           = $this->getTable();
      $tab[50]['field']           = 'dist_list';
      $tab[50]['name']            = __('Distribution List');

      // Last DR
	  $tab[55]['table']          = $this->getTable();
      $tab[55]['field']          = 'date_last_dr';
      $tab[55]['name']           = __('Last disaster recovery tested');
      $tab[55]['datatype']       = 'date';	  

      // Next DR
	  $tab[60]['table']          = $this->getTable();
      $tab[60]['field']          = 'date_next_dr';
      $tab[60]['name']           = __('Next disaster recovery planning');
      $tab[60]['datatype']       = 'date';	  

      //Editor
	  $tab[65]['table']          = 'glpi_manufacturers';
      $tab[65]['field']          = 'name';
      $tab[65]['name']           = __('Editor', 'services');
      $tab[65]['datatype']       = 'dropdown';

      // Is DR
	  $tab[70]['table']          = $this->getTable();
      $tab[70]['field']          = 'is_disaster_recovery';
      $tab[70]['name']           = __('Is Disaster Recovery');
      $tab[70]['datatype']       = 'bool';

	  // Is DR documented
	  $tab[75]['table']          = $this->getTable();
      $tab[75]['field']          = 'is_disaster_recovery_documented';
      $tab[75]['name']           = __('Is Disaster Recovery Documented');
      $tab[75]['datatype']       = 'bool';

	  // DR doc URL
	  $tab[76]['table']          = $this->getTable();
      $tab[76]['field']          = 'dr_docs_url';
      $tab[76]['name']           = __('DR Documentation URL', 'services');
      $tab[76]['datatype']       = 'weblink';
	  
	  // Entity complete name
      $tab[80]['table']          = 'glpi_entities';
      $tab[80]['field']          = 'completename';
      $tab[80]['name']           = __('Entity');
      $tab[80]['datatype']       = 'dropdown';

      // RPO
	  $tab[85]['table']           = 'glpi_plugin_services_servicerpos';
      $tab[85]['field']           = 'name';
      $tab[85]['name']            = PluginServicesServiceRpo::getTypeName(1);
      $tab[85]['datatype']        = 'dropdown';	  
	  
      // RTO
	  $tab[90]['table']           = 'glpi_plugin_services_servicertos';
      $tab[90]['field']           = 'name';
      $tab[90]['name']            = PluginServicesServiceRto::getTypeName(1);
      $tab[90]['datatype']        = 'dropdown';

      // Sharepoint URL
	  $tab[95]['table']          = $this->getTable();
      $tab[95]['field']          = 'backoffice';
      $tab[95]['name']           = __('Sharepoint URL', 'services');
      $tab[95]['datatype']       = 'weblink';

      // Supplier
	  $tab[100]['table']           = 'glpi_suppliers';
      $tab[100]['field']           = 'name';
      $tab[100]['name']            = __('Supplier');
      $tab[100]['datatype']        = 'itemlink';

	  // IT owner
	  $tab[101]['table']           = 'glpi_users';
      $tab[101]['field']           = 'name';
      $tab[101]['linkfield']       = 'users_id_itowner';
      $tab[101]['name']            = __('Product Owner');
      $tab[101]['datatype']        = 'dropdown';
      $tab[101]['right']           = 'interface';
	  
	  // Technicial owner
	  $tab[102]['table']           = 'glpi_users';
      $tab[102]['field']           = 'name';
      $tab[102]['linkfield']       = 'users_id_tech';
      $tab[102]['name']            = __('Technicial Owner');
      $tab[102]['datatype']        = 'dropdown';
      $tab[102]['right']           = 'interface';

	  //technical groups
      $tab[110]['table']          = 'glpi_groups';
      $tab[110]['field']          = 'name';
      $tab[110]['linkfield']      = 'groups_id_tech';
      $tab[110]['name']           = __('Technical Owner Group');
      $tab[110]['condition']      = '`is_assign`';
      $tab[110]['datatype']       = 'dropdown';

	  // Secondary Technicial owner
	  $tab[115]['table']           = 'glpi_users';
      $tab[115]['field']           = 'name';
      $tab[115]['linkfield']       = 'users_id_sectech';
      $tab[115]['name']            = __('Secondary Technicial Twner');
      $tab[115]['datatype']        = 'dropdown';
      $tab[115]['right']           = 'interface';

	  // Service support time
      $tab[120]['table']           = 'glpi_plugin_services_servicesupports';
      $tab[120]['field']           = 'name';
      $tab[120]['name']            = PluginServicesServiceSupport::getTypeName(1);
      $tab[120]['datatype']        = 'dropdown';


	  // DR Test Type
      $tab[121]['table']           = 'glpi_plugin_services_servicedrtesttypes';
      $tab[121]['field']           = 'name';
      $tab[121]['name']            = PluginServicesServiceDrTestType::getTypeName(1);
      $tab[121]['datatype']        = 'dropdown';

	  // DR Solution Type
      $tab[122]['table']           = 'glpi_plugin_services_servicedrsolutiontypes';
      $tab[122]['field']           = 'name';
      $tab[122]['name']            = PluginServicesServiceDrSolutionType::getTypeName(1);
      $tab[122]['datatype']        = 'dropdown';

	  // Tiers
	  $tab[125]['table']           = 'glpi_plugin_services_servicetiers';
      $tab[125]['field']           = 'name';
      $tab[125]['name']            = PluginServicesServiceTier::getTypeName(1);
      $tab[125]['datatype']        = 'dropdown';

	  // Is Corp DBA Managed
      $tab[128]['table']          = $this->getTable();
      $tab[128]['field']          = 'Is_Corp_DBA_Managed';
      $tab[128]['name']           = __('Is Corp DBA Managed');
      $tab[128]['datatype']       = 'bool';
	  
	  // Is helpdesk visible
      $tab[129]['table']          = $this->getTable();
      $tab[129]['field']          = 'is_helpdesk_visible';
      $tab[129]['name']           = __('Associable to a ticket');
      $tab[129]['datatype']       = 'bool';

	  // TCO
      $tab[130]['table']           = 'glpi_plugin_services_servicetypes';
      $tab[130]['field']           = 'name';
      $tab[130]['name']            = PluginServicesServiceType::getTypeName(1);
      $tab[130]['datatype']        = 'dropdown';
      
	  // ID
	  $tab[135]['table']          = $this->getTable();
      $tab[135]['field']          = 'id';
      $tab[135]['name']           = __('ID');
      $tab[135]['datatype']       = 'number';
  
	  //Last update
      $tab[145]['table']          = $this->getTable();
      $tab[145]['field']          = 'date_mod';
      $tab[145]['massiveaction']  = false;
      $tab[145]['name']           = __('Last update');
      $tab[145]['datatype']       = 'datetime';
	  
	  // is_recursive
	  $tab[150]['table']           = $this->getTable();
      $tab[150]['field']           = 'is_recursive';
      $tab[150]['name']            = __('Child entities');
      $tab[150]['datatype']        = 'bool';
      return $tab;
   } */

   function rawSearchOptions() {

      $tab = [];

      $tab[] = [
         'id'                 => 'common',
         'name'               => self::getTypeName(2)
      ];
	  // Name
      $tab[] = [
         'id'                 => '1',
         'table'              => $this->getTable(),
         'field'              => 'name',
         'name'               => __('Product Name'),
         'datatype'           => 'itemlink',
         'itemlink_type'      => $this->getType()
      ];

      $tab[] = [
         'id'                 => '5',
         'table'              => 'glpi_users',
         'field'              => 'name',
         'linkfield'          => 'users_id_app',
         'name'               => __('Application Specialist'),
         'datatype'           => 'dropdown',
		 'right'              => 'interface'
      ];

      $tab[] = [
         'id'                 => '10',
         'table'              => 'glpi_users',
         'field'              => 'name',
         'linkfield'          => 'users_id_secapp',
         'name'               => __('Secondary Application Specialist'),
         'datatype'           => 'dropdown',
		 'right'              => 'interface'
      ];

      /* $tab = array_merge($tab, Location::rawSearchOptionsToAdd()); */
	  
      $tab[] = [
         'id'                 => '15',
         'table'              => 'glpi_plugin_services_services_items',
         'field'              => 'items_id',
         'nosearch'           => true,
         'massiveaction'      => false,
         'name'               => _n('Associated item', 'Associated items', 2),
         'forcegroupby'       => true,
         'joinparams'         => ['jointype'           => 'child']
      ];

      $tab[] = [
         'id'                 => '20',
         'table'              => 'glpi_users',
         'field'              => 'name',
         'linkfield'          => 'users_id_bus',
         'name'               => __('Business owner'),
         'datatype'           => 'dropdown',
		 'right'              => 'interface'
      ];	  

      $tab[] = [
         'id'                 => '25',
         'table'              => 'glpi_plugin_services_servicebuspriorities',
         'field'              => 'name',
         'name'               => PluginServicesServicebuspriority::getTypeName(1),
         'datatype'           => 'dropdown'
      ];

	  // Business risks
      $tab[] = [
         'id'                 => '30',
         'table'              => 'glpi_plugin_services_servicebusrisks',
         'field'              => 'name',
         'name'               => PluginServicesServiceBusRisk::getTypeName(1),
         'datatype'           => 'dropdown'
      ];	
	  
	  // Categories	  
      $tab[] = [
         'id'                 => '35',
         'table'              => 'glpi_itilcategories',
         'field'              => 'completename',
         'name'               => __('Category'),
         'datatype'           => 'dropdown'
      ];	
	  
	  // Comments
      $tab[] = [
         'id'                 => '40',
         'table'              => $this->getTable(),
         'field'              => 'comment',
         'name'               => __('Comments'),
         'datatype'           => 'text'
      ];
	  
	  // Dashboard URL
      $tab[] = [
         'id'                 => '45',
         'table'              => $this->getTable(),
         'field'              => 'Address',
         'name'               => __('Dashboard URL'),
         'datatype'           => 'weblink'
      ];
	  
      // Distribution list
	  $tab[] = [
         'id'                 => '50',
         'table'              => $this->getTable(),
         'field'              => 'dist_list',
         'name'               => __('Distribution List'),
      ];
	  
	  // Last DR
      $tab[] = [
         'id'                 => '55',
         'table'              => $this->getTable(),
         'field'              => 'date_last_dr',
         'massiveaction'      => false,
         'name'               => __('Last disaster recovery tested'),
         'datatype'           => 'date'
      ];

	  // Next DR
      $tab[] = [
         'id'                 => '60',
         'table'              => $this->getTable(),
         'field'              => 'date_next_dr',
         'massiveaction'      => false,
         'name'               => __('Next disaster recovery planning'),
         'datatype'           => 'date'
      ];

	  // Editor	  
      $tab[] = [
         'id'                 => '65',
         'table'              => 'glpi_manufacturers',
         'field'              => 'name',
         'name'               => __('Editor'),
         'datatype'           => 'dropdown'
      ];	

      // Is DR
      $tab[] = [
         'id'                 => '70',
         'table'              => $this->getTable(),
         'field'              => 'is_disaster_recovery',
         'name'               => __('Is Disaster Recovery'),
         'datatype'           => 'bool'
      ];
	  
      // Is DR documented
      $tab[] = [
         'id'                 => '75',
         'table'              => $this->getTable(),
         'field'              => 'is_disaster_recovery_documented',
         'name'               => __('Is DR documented'),
         'datatype'           => 'bool'
      ];

	  // DR doc URL
      $tab[] = [
         'id'                 => '76',
         'table'              => $this->getTable(),
         'field'              => 'dr_docs_url',
         'name'               => __('DR Documentation URL'),
         'datatype'           => 'weblink'
      ];
	  
	  // Entity complete name
      $tab[] = [
         'id'                 => '80',
         'table'              => 'glpi_entities',
         'field'              => 'completename',
         'name'               => __('Entity'),
         'datatype'           => 'dropdown'
      ];
	  
      // RPO
      $tab[] = [
         'id'                 => '85',
         'table'              => 'glpi_plugin_services_servicerpos',
         'field'              => 'name',
         'name'               => PluginServicesServiceRpo::getTypeName(1),
         'datatype'           => 'dropdown'
      ];

      // RTO
      $tab[] = [
         'id'                 => '90',
         'table'              => 'glpi_plugin_services_servicertos',
         'field'              => 'name',
         'name'               => PluginServicesServiceRto::getTypeName(1),
         'datatype'           => 'dropdown'
      ]; 
	  

      // Sharepoint URL
      $tab[] = [
         'id'                 => '95',
         'table'              => $this->getTable(),
         'field'              => 'backoffice',
         'name'               => __('Sharepoint URL'),
         'datatype'           => 'weblink'
      ];
	  
      // Supplier
      $tab[] = [
         'id'                 => '100',
         'table'              => 'glpi_suppliers',
         'field'              => 'name',
         'name'               => __('Supplier'),
         'datatype'           => 'itemlink'
      ];

	  // IT owner
      $tab[] = [
         'id'                 => '101',
         'table'              => 'glpi_users',
         'field'              => 'name',
		 'linkfield'       	  => 'users_id_itowner',
         'name'               => __('Product Owner'),
         'datatype'           => 'dropdown',
		 'right'           => 'interface'
      ]; 
	  
	  // Technicial owner
      $tab[] = [
         'id'                 => '102',
         'table'              => 'glpi_users',
		 'linkfield'       	  => 'users_id_tech',
         'field'              => 'name',
         'name'               => __('Technicial Owner'),
         'datatype'           => 'dropdown',
		 'right'           => 'interface'
      ]; 

	  //technical groups
      $tab[] = [
         'id'                 => '110',
         'table'              => 'glpi_groups',
         'field'              => 'name',
		 'linkfield'          => 'groups_id_tech',
         'name'               => __('Technical Owner Group'),
         'datatype'           => 'dropdown',
		 'condition'           => '`is_assign`'
      ]; 

	  // Secondary Technicial owner
      $tab[] = [
         'id'                 => '115',
         'table'              => 'glpi_users',
         'field'              => 'name',
		 'linkfield'          => 'users_id_sectech',
		 'name'               => __('Secondary Technicial Owner'),
         'datatype'           => 'dropdown',
		 'right'           	  => 'interface'
      ]; 

	  // Service status
     # $tab[] = [
     #    'id'                 => '118',
     #    'table'              => 'glpi_plugin_services_servicestatus',
     #    'field'              => 'name',
     #    'name'               => PluginServicesServiceStatus::getTypeName(1),
     #    'datatype'           => 'dropdown'
     # ]; 
	  
	  // Service support time
      $tab[] = [
         'id'                 => '120',
         'table'              => 'glpi_plugin_services_servicesupports',
         'field'              => 'name',
         'name'               => PluginServicesServiceSupport::getTypeName(1),
         'datatype'           => 'dropdown'
      ]; 

	  // DR Test Type
      $tab[] = [
         'id'                 => '121',
         'table'              => 'glpi_plugin_services_servicedrtesttypes',
         'field'              => 'name',
         'name'               => PluginServicesServiceDrTestType::getTypeName(1),
         'datatype'           => 'dropdown'
      ]; 

	  // DR Solution Type
      $tab[] = [
         'id'                 => '122',
         'table'              => 'glpi_plugin_services_servicedrsolutiontypes',
         'field'              => 'name',
         'name'               => PluginServicesServiceDrSolutionType::getTypeName(1),
         'datatype'           => 'dropdown'
      ]; 

	  // Tiers
      $tab[] = [
         'id'                 => '125',
         'table'              => 'glpi_plugin_services_servicetiers',
         'field'              => 'name',
         'name'               => PluginServicesServiceTier::getTypeName(1),
         'datatype'           => 'dropdown'
      ]; 

	  // Is CORP DBA Managed
      $tab[] = [
         'id'                 => '128',
         'table'              => $this->getTable(),
         'field'              => 'is_corp_dba_managed',
         'name'               => __('Is CORP DBA Managed'),
         'datatype'           => 'bool'
      ];

	  // Is helpdesk visible
      $tab[] = [
         'id'                 => '129',
         'table'              => $this->getTable(),
         'field'              => 'is_helpdesk_visible',
         'name'               => __('Associable to a ticket'),
         'datatype'           => 'bool'
      ];

	  // TCO
      $tab[] = [
         'id'                 => '130',
         'table'              => 'glpi_plugin_services_servicetypes',
         'field'              => 'name',
         'name'               => PluginServicesServiceType::getTypeName(1),
         'datatype'           => 'dropdown'
      ]; 

	  // ID
      $tab[] = [
         'id'                 => '135',
         'table'              => $this->getTable(),
         'field'              => 'id',
         'name'               => __('ID'),
         'datatype'           => 'number'
      ];
  
	  //Last update
      $tab[] = [
         'id'                 => '145',
         'table'              => $this->getTable(),
         'field'              => 'date_mod',
         'massiveaction'      => false,
         'name'               => __('Last update'),
         'datatype'           => 'date'
      ];

	  // is_recursive
      $tab[] = [
         'id'                 => '150',
         'table'              => $this->getTable(),
         'field'              => 'is_recursive',
         'name'               => __('Child entities'),
         'datatype'           => 'bool'
      ];
	  
	        return $tab;
   }
   //define header form
   /**
    * @param array $options
    *
    * @return array
    */
   function defineTabs($options = []) {

      $ong = [];
      $this->addDefaultFormTab($ong);
      $this->addStandardTab('PluginServicesService_Item', $ong, $options);
      $this->addStandardTab('Ticket', $ong, $options);
      $this->addStandardTab('KnowbaseItem_Item', $ong, $options);
      $this->addStandardTab('Item_Problem', $ong, $options);
      $this->addStandardTab('Change_Item', $ong, $options);
      $this->addStandardTab('Document_Item', $ong, $options);
      $this->addStandardTab('Contract_Item', $ong, $options);
      $this->addStandardTab('Address', $ong, $options);
      $this->addStandardTab('Notepad', $ong, $options);
      $this->addStandardTab('Log', $ong, $options);
      return $ong;
   }
   
   //define fonction date
   function prepareInputForAdd($input) {

      if (isset($input['date_query']) 
         && empty($input['date_query'])) 
            $input['date_query']='NULL';
      if (isset($input['date_next_dr']) 
         && empty($input['date_next_dr'])) 
            $input['date_next_dr']='NULL';

      return $input;
   }

   function prepareInputForUpdate($input) {

      if (isset($input['date_query']) 
         && empty($input['date_query'])) 
            $input['date_query']='NULL';
      if (isset($input['date_next_dr']) 
         && empty($input['date_next_dr'])) 
            $input['date_next_dr']='NULL';

      return $input;
   }

   /**
    * Return the SQL command to retrieve linked object
    *
    * @return a SQL command which return a set of (itemtype, items_id)
    **/
   function getSelectLinkedItem() {

      return "SELECT `itemtype`, `items_id`
              FROM `glpi_plugin_services_services_items`
              WHERE `plugin_services_services_id`='" . $this->fields['id'] . "'";
   }


   function showForm($ID, $options = []) {

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      //name of services
      echo "<td>".__('Service Name')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this,"name");
      echo "</td>";
	  
	  //Service Status
      #echo "<td>".PluginServicesServiceStatus::getTypeName(1)."</td>";
      #echo "<td>";
      #Dropdown::show('PluginServicesServiceStatus',
      #      array('value' => $this->fields["plugin_services_servicestatus_id"]));
      #echo "</td>";
	  
	  //Service Support Level
      echo "<td>".PluginServicesServiceSupport::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServiceSupport',
            array('value' => $this->fields["plugin_services_servicesupports_id"]));
      echo "</td>";

	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";

      //ITILCategory of services
      echo "<td>".__('Category')."</td><td>";
      Dropdown::show('ITILCategory', array('name' => "itilcategories_id",
                                    'value'  => $this->fields["itilcategories_id"]));
      echo "</td>";
	  
      //Distribution List
      echo "<td>".__('Distribution List')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this, "dist_list", array('size' => "15"));
      echo "</td>";

	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";

	  //type of services
      echo "<td>".PluginServicesServiceType::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServiceType',
                  array('value'  => $this->fields["plugin_services_servicetypes_id"],
                           'entity' => $this->fields["entities_id"]));
      echo "</td>";
      
	  //supplier of services
      echo "<td>".__('Supplier')."</td>";
      echo "<td>";
      Dropdown::show('Supplier', array('value'  => $this->fields["suppliers_id"],
                                       'entity' => $this->fields["entities_id"]));
      echo "</td>";

	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";

      //url of services
      echo "<td>".__('Service URL')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this, "Address", array('size' => "65"));
      echo "</td>";

      //manufacturer of services
      echo "<td>".__('Editor', 'services')."</td>";
      echo "<td>";
      Dropdown::show('Manufacturer', array('value'  => $this->fields["manufacturers_id"],
                                           'entity' => $this->fields["entities_id"]));
      echo "</td>";
	  
	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";
	  
      //backoffice of services
      echo "<td>".__('Service Admin URL', 'services')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this, "backoffice", array('size' => "65"));
      echo "</td>";

      //is_corp_dba_managed
      echo "<td>" . __('Is CORP DBA Managed') . "</td><td>";
      Dropdown::showYesNo('is_corp_dba_managed', $this->fields['is_corp_dba_managed']);
      echo "</td>";

	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";	  
 
	  //is_helpdesk_visible
      echo "<td>" . __('Associable to a ticket') . "</td><td>";
      Dropdown::showYesNo('is_helpdesk_visible', $this->fields['is_helpdesk_visible']);
      echo "</td>";
	  
	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";
	  
      //comments of services
      echo "<td class='top center' colspan='4'>".__('Comments')."</td>";
      echo "</tr>";
      echo "<tr class='tab_bg_1'>";
      echo "<td class='top center' colspan='4'><textarea cols='125' rows='3' name='comment' >".
            $this->fields["comment"]."</textarea>";
      echo "</tr>";

	  // ---------------------------------- Section Owner ---------------------------------------
	  echo "<div class='spaced'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";
      echo "<th>".__('Owner information', 'Services')."</th>";
      echo "</tr>";

	  echo "<tr class='tab_bg_1'>";  
	  //IT Owner
      echo "<td>".__('Service Owner')."</td><td>";
      User::dropdown(array('name' => "users_id_itowner",
                           'value'  => $this->fields["users_id_itowner"],
                           'entity' => $this->fields["entities_id"],
                           'right'  => 'interface'));
      echo "</td>";
	  echo "</tr>";

	  //  ---- End of 2 -----	  
	  
	  //Service specialist
	  echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Service Specialist')."</td><td>";
      User::dropdown(array('name' => "users_id_app",
                           'value'  => $this->fields["users_id_app"],
                           'entity' => $this->fields["entities_id"],
                           'right'  => 'interface'));
      echo "</td>";
	  
	  //Technical Specialist
      echo "<td>".__('Technical Specialist')."</td><td>";
      User::dropdown(array('name' => "users_id_tech",
                           'value'  => $this->fields["users_id_tech"],
                           'entity' => $this->fields["entities_id"],
                           'right'  => 'interface'));
      echo "</td>";
	  
	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";
	  
	  //Backup service specialist
      echo "<td>".__('Backup Service Specialist')."</td><td>";
      User::dropdown(array('name' => "users_id_secapp",
                           'value'  => $this->fields["users_id_secapp"],
                           'entity' => $this->fields["entities_id"],
                           'right'  => 'interface'));
      echo "</td>";

	  //secondary Technical Specialist
      echo "<td>".__('Backup Technical Specialist')."</td><td>";
      User::dropdown(array('name' => "users_id_sectech",
                           'value'  => $this->fields["users_id_sectech"],
                           'entity' => $this->fields["entities_id"],
                           'right'  => 'interface'));
      echo "</td>";
	 
	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";
	  
	  //tech groups
      echo "<td>".__('Technical Group')."</td><td>";
      Dropdown::show('Group', array('name' => "groups_id_tech",
                                    'value'  => $this->fields["groups_id_tech"],
                                    'entity' => $this->fields["entities_id"],
                                    'condition' => ['is_assign' => 1]));
      echo "</td>";
	  
	  //Bus user
	        echo "<td>".__('Management Owner')."</td><td>";
      User::dropdown(array('name' => "users_id_bus",
                           'value'  => $this->fields["users_id_bus"],
                           'entity' => $this->fields["entities_id"],
                           'right'  => 'interface'));
	  
      echo "</td>";
	  
	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";
      
	  // ---------------------------------- Section Business information ---------------------------------------
	  echo "<div class='spaced'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";
      echo "<th>".__('Business information priorities', 'Services')."</th>";
      echo "</tr>";

	  //Business Risk
      echo "<td>".PluginServicesServiceBusRisk::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServiceBusRisk',
               array('value' => $this->fields["plugin_services_servicebusrisks_id"]));
      echo "</td>";
	  
	  //RTO
      echo "<td>".PluginServicesServiceRto::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServiceRto',
            array('value' => $this->fields["plugin_services_servicertos_id"]));
      echo "</td>";

	  //  ---- End of 2 -----	  
	  echo "<tr class='tab_bg_1'>";
	  
      //Business Priority
      echo "<td>".PluginServicesServicebuspriority::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServicebuspriority',
               array('value' => $this->fields["plugin_services_servicebuspriorities_id"]));
      echo "</td>";
	  
	  //RPO
      echo "<td>".PluginServicesServiceRpo::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServiceRpo',
            array('value' => $this->fields["plugin_services_servicerpos_id"]));
      echo "</td>";
	  
	  //  ---- End of 2 -----

	  
	  // ---------------------------------- Section DRP ---------------------------------------
	  echo "<div class='spaced'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";
      echo "<th>".__('Disaster Recovery information', 'Services')."</th>";
      echo "</tr>";
	  
	  echo "<tr class='tab_bg_1'>";
	  
	  //Is Disaster Recovery
      echo "<td>" . __('Is Disaster Recovery') . "</td><td>";
      Dropdown::showYesNo('is_disaster_recovery', $this->fields['is_disaster_recovery']);
      echo "</td>";
	  
	  //Is Disaster Recovery Documented
      echo "<td>" . __('Is Disaster Recovery Documented') . "</td><td>";
      Dropdown::showYesNo('is_disaster_recovery_documented', $this->fields['is_disaster_recovery_documented']);
      echo "</td>";

	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";
	  
	  //DR Documentation URL
      echo "<td>".__('DR Documentation URL', 'services')."</td>";
      echo "<td>";
      Html::autocompletionTextField($this, "DR_docs_url", array('size' => "65"));
      echo "</td>";
	  
	  //Tier Level
      echo "<td>".PluginServicesServiceTier::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServiceTier',
      array('value' => $this->fields["plugin_services_servicetiers_id"]));
      echo "</td>";
	  
	  //  ---- End of 2 -----

	  echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Last disaster recovery tested')."</td>";
      echo "<td>";
      Html::showDateField("date_last_dr",['value' => $this->fields["date_last_dr"],'maybeempty' => true]);
      echo "</td>";	  
	  
      echo "<td>".__('Next disaster recovery planning')."</td>";
      echo "<td>";
      Html::showDateField("date_next_dr",['value' => $this->fields["date_next_dr"],'maybeempty' => true]);
      echo "</td>";	  
	   
	  //  ---- End of 2 -----
	  echo "<tr class='tab_bg_1'>";

	  //DR Test Type
      echo "<td>".PluginServicesServiceDrTestType::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServiceDrTestType',
               array('value' => $this->fields["plugin_services_servicedrtesttypes_id"]));
      echo "</td>";
	  
	  //DR Solution Type
      echo "<td>".PluginServicesServiceDrSolutionType::getTypeName(1)."</td>";
      echo "<td>";
      Dropdown::show('PluginServicesServiceDrSolutionType',
            array('value' => $this->fields["plugin_services_servicedrsolutiontypes_id"]));
      echo "</td>";

	  //  ---- End of 2 -----
 
	  	  echo "<tr class='tab_bg_1'>";
      $this->showFormButtons($options);

      return true;
   }
   

   
  /**
    * Make a select box for link services
    *
    * Parameters which could be used in options array :
    *    - name : string / name of the select (default is plugin_services_services_id)
    *    - entity : integer or array / restrict to a defined entity or array of entities
    *                   (default -1 : no restriction)
    *    - used : array / Already used items ID: not to display in dropdown (default empty)
    *
    * @param $options array of possible options
    *
    * @return nothing (print out an HTML select box)
    **/
   static function dropdownService($options = []) {
      global $DB, $CFG_GLPI;

      $p['name']    = 'plugin_services_services_id';
      $p['entity']  = '';
      $p['used']    = [];
      $p['display'] = true;

      if (is_array($options) && count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }
      $dbu = new DbUtils();
      $where = " WHERE `glpi_plugin_services_services`.`is_deleted` = '0' " .
               $dbu->getEntitiesRestrictRequest("AND", "glpi_plugin_services_services", '', $p['entity'], true);

      $p['used'] = array_filter($p['used']);
      if (count($p['used'])) {
         $where .= " AND `id` NOT IN (0, " . implode(",", $p['used']) . ")";
      }

      $query  = "SELECT *
                FROM `glpi_plugin_services_servicetypes`
                WHERE `id` IN (SELECT DISTINCT `plugin_services_servicetypes_id`
                               FROM `glpi_plugin_services_services`
                             $where)
                ORDER BY `name`";
      $result = $DB->query($query);

      $values = [0 => Dropdown::EMPTY_VALUE];

      while ($data = $DB->fetchassoc($result)) {
         $values[$data['id']] = $data['name'];
      }
      $rand     = mt_rand();
      $out      = Dropdown::showFromArray('_servicetype', $values, ['width'   => '30%',
                                                                                'rand'    => $rand,
                                                                                'display' => false]);
      $field_id = Html::cleanId("dropdown__servicetype$rand");

      $params = ['servicetype' => '__VALUE__',
                      'entity'             => $p['entity'],
                      'rand'               => $rand,
                      'myname'             => $p['name'],
                      'used'               => $p['used']];

      $out .= Ajax::updateItemOnSelectEvent($field_id, "show_" . $p['name'] . $rand,
                                            $CFG_GLPI["root_doc"] . "/plugins/services/ajax/dropdownTypeServices.php",
                                            $params, false);
      $out .= "<span id='show_" . $p['name'] . "$rand'>";
      $out .= "</span>\n";

      $params['servicetype'] = 0;
      $out .= Ajax::updateItem("show_" . $p['name'] . $rand,
                               $CFG_GLPI["root_doc"] . "/plugins/services/ajax/dropdownTypeServices.php",
                               $params, false);
      if ($p['display']) {
         echo $out;
         return $rand;
      }
      return $out;
   }


   /**
    * For other plugins, add a type to the linkable types
    *
    * @since version 1.3.0
    *
    * @param $type string class name
    **/
   static function registerType($type) {
      if (!in_array($type, self::$types)) {
         self::$types[] = $type;
      }
   }


   /**
    * Type than could be linked to a Rack
    *
    * @param $all boolean, all type, or only allowed ones
    *
    * @return array of types
    **/
   static function getTypes($all = false) {

      if ($all) {
         return self::$types;
      }

      // Only allowed types
      $types = self::$types;

      foreach ($types as $key => $type) {
         if (!class_exists($type)) {
            continue;
         }

         $item = new $type();
         if (!$item->canView()) {
            unset($types[$key]);
         }
      }
      return $types;
   }
   
   
   /**
    * @since version 0.85
    *
    * @see CommonDBTM::getSpecificMassiveActions()
    *
    * @param null $checkitem
    *
    * @return an
    */
   function getSpecificMassiveActions($checkitem = null) {
      $isadmin = static::canUpdate();
      $actions = parent::getSpecificMassiveActions($checkitem);

      if (Session::getCurrentInterface() == 'central') {
         if ($isadmin) {
            $actions['PluginServicesService' . MassiveAction::CLASS_ACTION_SEPARATOR . 'install']   = _x('button', 'Associate');
            $actions['PluginServicesService' . MassiveAction::CLASS_ACTION_SEPARATOR . 'uninstall'] = _x('button', 'Dissociate');

            if (Session::haveRight('transfer', READ)
                && Session::isMultiEntitiesMode()) {
               $actions['PluginServicesService' . MassiveAction::CLASS_ACTION_SEPARATOR . 'transfer'] = __('Transfer');
            }
         }
      }
      return $actions;
   }
   
   
   /**
    * @since version 0.85
    *
    * @see CommonDBTM::showMassiveActionsSubForm()
    *
    * @param MassiveAction $ma
    *
    * @return bool|false
    */
   static function showMassiveActionsSubForm(MassiveAction $ma) {

      switch ($ma->getAction()) {
         case 'plugin_services_add_item':
            self::dropdownService([]);
            echo "&nbsp;" .
                 Html::submit(_x('button', 'Post'), ['name' => 'massiveaction']);
            return true;
            break;
         case "install" :
            Dropdown::showSelectItemFromItemtypes(['items_id_name' => 'item_item',
                                                        'itemtype_name' => 'typeitem',
                                                        'itemtypes'     => self::getTypes(true),
                                                        'checkright'
                                                                        => true,
                                                  ]);
            echo Html::submit(_x('button', 'Post'), ['name' => 'massiveaction']);
            return true;
            break;
         case "uninstall" :
            Dropdown::showSelectItemFromItemtypes(['items_id_name' => 'item_item',
                                                        'itemtype_name' => 'typeitem',
                                                        'itemtypes'     => self::getTypes(true),
                                                        'checkright'
                                                                        => true,
                                                  ]);
            echo Html::submit(_x('button', 'Post'), ['name' => 'massiveaction']);
            return true;
            break;
         case "transfer" :
            Dropdown::show('Entity');
            echo Html::submit(_x('button', 'Post'), ['name' => 'massiveaction']);
            return true;
            break;
      }
      return parent::showMassiveActionsSubForm($ma);
   }
   
   
   /**
    * @since version 0.85
    *
    * @see CommonDBTM::processMassiveActionsForOneItemtype()
    *
    * @param MassiveAction $ma
    * @param CommonDBTM    $item
    * @param array         $ids
    *
    * @return nothing|void
    */
   static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item,
                                                       array $ids) {

      $web_item = new PluginServicesService_Item();

      switch ($ma->getAction()) {
         case "plugin_services_add_item":
            $input = $ma->getInput();
            foreach ($ids as $id) {
               $input = ['plugin_services_services_id' => $input['plugin_services_services_id'],
                              'items_id'                                  => $id,
                              'itemtype'                                  => $item->getType()];
               if ($web_item->can(-1, UPDATE, $input)) {
                  if ($web_item->add($input)) {
                     $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_OK);
                  } else {
                     $ma->itemDone($item->getType(), $ids, MassiveAction::ACTION_KO);
                  }
               } else {
                  $ma->itemDone($item->getType(), $ids, MassiveAction::ACTION_KO);
               }
            }

            return;
         case "transfer" :
            $input = $ma->getInput();
            if ($item->getType() == 'PluginServicesService') {
               foreach ($ids as $key) {
                  $item->getFromDB($key);
                  $type = PluginServicesServiceType::transfer($item->fields["plugin_services_servicetypes_id"], $input['entities_id']);
                  if ($type > 0) {
                     $values["id"]                                            = $key;
                     $values["plugin_services_servicetypes_id"] = $type;
                     $item->update($values);
                  }

                  unset($values);
                  $values["id"]          = $key;
                  $values["entities_id"] = $input['entities_id'];

                  if ($item->update($values)) {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
                  } else {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
                  }
               }
            }
            return;

         case 'install' :
            $input = $ma->getInput();
            foreach ($ids as $key) {
               if ($item->can($key, UPDATE)) {
                  $values = ['plugin_services_services_id' => $key,
                                  'items_id'                                  => $input["item_item"],
                                  'itemtype'                                  => $input['typeitem']];
                  if ($web_item->add($values)) {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
                  } else {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
                  }
               } else {
                  $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_NORIGHT);
                  $ma->addMessage($item->getErrorMessage(ERROR_RIGHT));
               }
            }
            return;

         case 'uninstall':
            $input = $ma->getInput();
            foreach ($ids as $key) {
               if ($web_item->deleteItemByServicesAndItem($key, $input['item_item'], $input['typeitem'])) {
                  $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
               } else {
                  $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
               }
            }
            return;
      }
      parent::processMassiveActionsForOneItemtype($ma, $item, $ids);
   }
   
   /**
    * @param string     $link
    * @param CommonDBTM $item
    *
    * @return array
    */
   static function generateLinkContents($link, CommonDBTM $item) {

      if (strstr($link, "[SERVICE_URL]")) {
         $link = str_replace("[SERVICE_URL]", $item->fields['link'], $link);
         return [$link];
      }

      return parent::generateLinkContents($link, $item);
   }
}
?>
