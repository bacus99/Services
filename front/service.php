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

include ('../../../inc/includes.php');

//check environment meta-plugin installation for change header
$plugin = new Plugin();
if ($plugin->isActivated("environment")) {
   Html::header(PluginServicesService::getTypeName(2)
                  ,'',"plugins","pluginenvironmentdisplay","services");
}else {
   Html::header(PluginServicesService::getTypeName(2), '', "plugins","pluginservicesmenu");
}

$web = new PluginServicesService();
$web->checkGlobal(READ);

if ($web->canView()) {
   Search::show("PluginServicesService");

} else {
   
   Html::displayRightError();
}
Html::footer();

?>