<?php

/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 services plugin for GLPI
 Copyright (C) 2009-2016 by the services Development Team.

 https://github.com/InfotelGLPI/services
 -------------------------------------------------------------------------

 LICENSE

 This file is part of services.

 services is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 services is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with services. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

/**
 * Class PluginServicesMenu
 */
class PluginServicesMenu extends CommonGLPI {
   static $rightname = 'plugin_services';

   /**
    * @return translated
    */
   static function getMenuName() {
      return _n('TC Service', 'TC Services', 2, 'services');
   }

   /**
    * @return array
    */
   static function getMenuContent() {

      $menu                    = [];
      $menu['title']           = self::getMenuName();
      $menu['page']            = "/plugins/services/front/service.php";
      $menu['links']['search'] = PluginServicesService::getSearchURL(false);
      if (PluginServicesService::canCreate()) {
         $menu['links']['add'] = PluginServicesService::getFormURL(false);
      }

      return $menu;
   }

   static function removeRightsFromSession() {
      if (isset($_SESSION['glpimenu']['plugins']['types']['PluginServicesMenu'])) {
         unset($_SESSION['glpimenu']['plugins']['types']['PluginServicesMenu']);
      }
      if (isset($_SESSION['glpimenu']['plugins']['content']['pluginservicesmenu'])) {
         unset($_SESSION['glpimenu']['plugins']['content']['pluginservicesmenu']);
      }
   }
}
