-- Table structure for table `glpi_plugin_services_servicetypes`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_servicetypes` (
  `id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=141 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Indexes for table `glpi_plugin_services_servicetypes`
--
ALTER TABLE `glpi_plugin_services_servicetypes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `entities_id` (`entities_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `glpi_plugin_services_servicetypes`
--
ALTER TABLE `glpi_plugin_services_servicetypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=141;


--
-- Table structure for table `glpi_plugin_services_servicetiers`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_servicetiers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_services_servicetiers` (`id`, `name`, `comment`) VALUES
(1, 'Tier 0', ''),
(2, 'Tier 1', ''),
(3, 'Tier 2', ''),
(4, 'Tier 3', NULL);

--
-- Indexes for table `glpi_plugin_services_servicetiers`
--
ALTER TABLE `glpi_plugin_services_servicetiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);
  
-- AUTO_INCREMENT for table `glpi_plugin_services_servicetiers`
--
ALTER TABLE `glpi_plugin_services_servicetiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;



-- Table structure for table `glpi_plugin_services_services_items`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_services_items` (
  `id` int(11) NOT NULL,
  `plugin_services_services_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_plugin_services_services (id)',
  `items_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to various tables, according to itemtype (id)',
  `itemtype` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'see .class.php file'
) ENGINE=MyISAM AUTO_INCREMENT=7955 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Indexes for table `glpi_plugin_services_services_items`
--
ALTER TABLE `glpi_plugin_services_services_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unicity` (`plugin_services_services_id`,`items_id`,`itemtype`),
  ADD KEY `FK_device` (`items_id`,`itemtype`),
  ADD KEY `item` (`itemtype`,`items_id`);

  -- AUTO_INCREMENT for table `glpi_plugin_services_services_items`
--
ALTER TABLE `glpi_plugin_services_services_items`;


-- Table structure for table `glpi_plugin_services_servicesupports`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_servicesupports` (
  `id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `glpi_plugin_services_servicesupports`
--

INSERT INTO `glpi_plugin_services_servicesupports` (`id`, `entities_id`, `name`, `comment`) VALUES
(1, 0, '24/7', NULL),
(2, 0, '8x5', '');

-- Indexes for table `glpi_plugin_services_servicesupports`
--
ALTER TABLE `glpi_plugin_services_servicesupports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `entities_id` (`entities_id`);
  
-- AUTO_INCREMENT for table `glpi_plugin_services_servicesupports`
--
ALTER TABLE `glpi_plugin_services_servicesupports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;



  
-- Table structure for table `glpi_plugin_services_services`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_services` (
  `id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `is_recursive` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `backoffice` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plugin_services_servicetypes_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_plugin_services_servicetypes (id)',
  `plugin_services_servicetechnics_id` int(11) NOT NULL DEFAULT '0',
  `plugin_services_servicertos_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_plugin_services_serviceservertypes (id)',
  `plugin_services_servicerpos_id` int(11) NOT NULL DEFAULT '0',
  `plugin_services_servicesupports_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_plugin_services_serviceserversupports (id)',
  `plugin_services_servicetiers_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_plugin_services_servicetiers (id)',
  `plugin_services_servicebusrisks_id` int(11) NOT NULL DEFAULT '0',
  `plugin_services_servicebuspriorities_id` int(11) NOT NULL DEFAULT '0',
  `dist_list` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_id_tech` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_users (id)',
  `users_id_sectech` int(11) NOT NULL DEFAULT '0',
  `groups_id_tech` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_groups (id)',
  `users_id_bus` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_users (id)',
  `users_id_app` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_users (id)',
  `users_id_secapp` int(11) NOT NULL DEFAULT '0',
  `suppliers_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_suppliers (id)',
  `manufacturers_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_manufacturers (id)',
  `itilcategories_id` int(11) NOT NULL DEFAULT '0' COMMENT 'RELATION to glpi_itilcategories (id)',
  `date_mod` datetime DEFAULT NULL,
  `date_last_dr` date DEFAULT NULL,
  `date_next_dr` date DEFAULT NULL,
  `is_helpdesk_visible` int(11) NOT NULL DEFAULT '1',
  `comment` text COLLATE utf8_unicode_ci,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_disaster_recovery` int(5) NOT NULL DEFAULT '0',
  `is_disaster_recovery_documented` int(5) NOT NULL,
  `dr_docs_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=482 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Indexes for table `glpi_plugin_services_services`
--
ALTER TABLE `glpi_plugin_services_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `entities_id` (`entities_id`),
  ADD KEY `plugin_services_servicetypes_id` (`plugin_services_servicetypes_id`),
  ADD KEY `plugin_services_serviceservertypes_id` (`plugin_services_servicertos_id`),
  ADD KEY `plugin_services_servicetechnics_id` (`plugin_services_servicetiers_id`),
  ADD KEY `users_id_tech` (`users_id_tech`),
  ADD KEY `groups_id_tech` (`groups_id_tech`),
  ADD KEY `suppliers_id` (`suppliers_id`),
  ADD KEY `manufacturers_id` (`manufacturers_id`),
  ADD KEY `locations_id` (`itilcategories_id`),
  ADD KEY `date_mod` (`date_mod`),
  ADD KEY `is_helpdesk_visible` (`is_helpdesk_visible`),
  ADD KEY `is_deleted` (`is_deleted`);

  --
-- AUTO_INCREMENT for table `glpi_plugin_services_services`
--
ALTER TABLE `glpi_plugin_services_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=482;


  
-- Table structure for table `glpi_plugin_services_servicertos`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_servicertos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `glpi_plugin_services_servicertos`
--

INSERT INTO `glpi_plugin_services_servicertos` (`id`, `name`, `comment`) VALUES
(5, '48h', ''),
(4, '&gt; 48h', ''),
(6, 'ND', ''),
(7, '4h', ''),
(8, '8h', ''),
(9, '24h', ''),
(10, '2h', '');


-- Indexes for table `glpi_plugin_services_servicertos`
--
ALTER TABLE `glpi_plugin_services_servicertos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);
  
--
-- AUTO_INCREMENT for table `glpi_plugin_services_servicertos`
--
ALTER TABLE `glpi_plugin_services_servicertos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;


  
-- Table structure for table `glpi_plugin_services_servicerpos`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_servicerpos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `glpi_plugin_services_servicerpos`
--

INSERT INTO `glpi_plugin_services_servicerpos` (`id`, `name`, `comment`) VALUES
(5, '48h', ''),
(4, '&gt; 48h', ''),
(6, 'ND', ''),
(7, '4h', ''),
(8, '8h', ''),
(9, '24h', ''),
(10, '2h', ''),
(11, '15 Min', '');

-- Indexes for table `glpi_plugin_services_servicerpos`
--
ALTER TABLE `glpi_plugin_services_servicerpos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

  --
-- AUTO_INCREMENT for table `glpi_plugin_services_servicerpos`
--
ALTER TABLE `glpi_plugin_services_servicerpos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;


-- Table structure for table `glpi_plugin_services_servicebusrisks`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_servicebusrisks` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `glpi_plugin_services_servicebusrisks`
--

INSERT INTO `glpi_plugin_services_servicebusrisks` (`id`, `name`, `comment`) VALUES
(1, 'Low', ''),
(2, 'Medium', ''),
(3, 'High', '');

-- Indexes for table `glpi_plugin_services_servicebusrisks`
--
ALTER TABLE `glpi_plugin_services_servicebusrisks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- AUTO_INCREMENT for table `glpi_plugin_services_servicebusrisks`
--
ALTER TABLE `glpi_plugin_services_servicebusrisks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;

  
-- Table structure for table `glpi_plugin_services_servicebuspriorities`
--

CREATE TABLE IF NOT EXISTS `glpi_plugin_services_servicebuspriorities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `glpi_plugin_services_servicebuspriorities`
--

INSERT INTO `glpi_plugin_services_servicebuspriorities` (`id`, `name`, `comment`) VALUES
(2, 'P1', ''),
(3, 'P2', ''),
(4, 'P3', ''),
(5, 'P4', NULL),
(1, 'P0', NULL);

-- Indexes for table `glpi_plugin_services_servicebuspriorities`
--
ALTER TABLE `glpi_plugin_services_servicebuspriorities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);
--
-- AUTO_INCREMENT for table `glpi_plugin_services_servicebuspriorities`
--
ALTER TABLE `glpi_plugin_services_servicebuspriorities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
