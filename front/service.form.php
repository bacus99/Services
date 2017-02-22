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

if (!isset($_GET["id"])) {
   $_GET["id"] = "";
}
if (!isset($_GET["withtemplate"])) {
   $_GET["withtemplate"] = "";
}

$web      = new PluginServicesService();
$web_item = new PluginServicesService_Item();

if (isset($_POST["add"])) {
   $web->check(-1,CREATE,$_POST);
   $newID= $web->add($_POST);
   if ($_SESSION['glpibackcreated']) {
      Html::redirect($web->getFormURL()."?id=".$newID);
   }
   Html::back();

} else if (isset($_POST["delete"])) {
   $web->check($_POST['id'],DELETE);
   $web->delete($_POST);
   $web->redirectToList();

} else if (isset($_POST["restore"])) {
   $web->check($_POST['id'],PURGE);
   $web->restore($_POST);
   $web->redirectToList();

} else if (isset($_POST["purge"])) {
   $web->check($_POST['id'],PURGE);
   $web->delete($_POST,1);
   $web->redirectToList();

} else if (isset($_POST["update"])) {
   $web->check($_POST['id'],UPDATE);
   $web->update($_POST);
   Html::back();

} else if (isset($_POST["additem"])) {
   
   if (!empty($_POST['itemtype'])&&$_POST['items_id']>0) {
       $web_item->check(-1,UPDATE,$_POST);
      $web_item->addItem($_POST);
   }
   Html::back();

} else if (isset($_POST["deleteitem"])) {
   foreach ($_POST["item"] as $key => $val) {
      $input = array('id' => $key);
      if ($val == 1) {
         $web_item->check($key, UPDATE);
         $web_item->delete($input);
      }
   }
   Html::back();

//unlink services to items of glpi from the items form
} else if (isset($_POST["deleteservices"])) {
   $input = array('id' => $_POST["id"]);
   $web_item->check($_POST["id"], UPDATE);
   $web_item->delete($input);
   Html::back();

} else {
   $web->checkGlobal(READ);

   //check environment meta-plugin installtion for change header
   $plugin = new Plugin();
   if ($plugin->isActivated("environment")) {
      Html::header(PluginServicesService::getTypeName(2),
                     '',"plugins","pluginenvironmentdisplay","services");
   } else {
      Html::header(PluginServicesService::getTypeName(2), '', "plugins","pluginservicesmenu");
   }
   //load services form
   $web->display($_GET);

   Html::footer();
}

?>