#
# Table structure for table `Accounts`
#

CREATE TABLE `Accounts` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `UserID` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `AccountID` int(10) unsigned NOT NULL default '0',
  `Password` varchar(255) NOT NULL default '',
  `RealName` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `AccountID` (`AccountID`),
  UNIQUE KEY `AccountKey` (`UserID`,`Name`),
  KEY `CustomerID` (`UserID`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Aliases`
#

CREATE TABLE `Aliases` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `DomainID` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `Type` enum('account','address','list') NOT NULL default 'account',
  `Data` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `AliasKey` (`DomainID`,`Name`),
  KEY `DomainID` (`DomainID`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Domains`
#

CREATE TABLE `Domains` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `UserID` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `DomainName` varchar(255) NOT NULL default '',
  `Registrar` varchar(255) NOT NULL default '',
  `Expiry` date NOT NULL default '0000-00-00',
  `Enabled` tinyint(1) NOT NULL default '0',
  `Mail` enum('primary','secondary','none') NOT NULL default 'primary',
  `MailPrimary` varchar(255) NOT NULL default '',
  `MailServerID` int(10) unsigned NOT NULL default '0',
  `DNS` enum('primary','secondary','none') NOT NULL default 'primary',
  `DNSPrimary` varchar(255) NOT NULL default '',
  `DNSSerial` varchar(10) NOT NULL default '',
  `DNSServerID` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `DomainName` (`DomainName`),
  KEY `UserID` (`UserID`),
  KEY `MailServerID` (`MailServerID`),
  KEY `DNSServerID` (`DNSServerID`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Hosts`
#

CREATE TABLE `Hosts` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `DomainID` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `Type` set('website','a','cname','subdomain') NOT NULL default 'a',
  `Data` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  KEY `DomainID` (`DomainID`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Lists`
#

CREATE TABLE `Lists` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `UserID` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `RealName` varchar(255) NOT NULL default '',
  `Owner` varchar(255) NOT NULL default '',
  `Public` tinyint(1) NOT NULL default '0',
  `ServerID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`),
  KEY `CustomerID` (`UserID`),
  KEY `ServerID` (`ServerID`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Servers`
#

CREATE TABLE `Servers` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  `FullName` varchar(255) NOT NULL default '',
  `Address` varchar(15) NOT NULL default '',
  `Live` tinyint(1) NOT NULL default '1',
  `Updated` datetime NOT NULL default '0000-00-00 00:00:00',
  `DNS` tinyint(1) NOT NULL default '0',
  `Mail` tinyint(1) NOT NULL default '0',
  `HTTP` tinyint(1) NOT NULL default '0',
  `DB` tinyint(1) NOT NULL default '0',
  `List` tinyint(1) NOT NULL default '0',
  `Shell` tinyint(1) NOT NULL default '0',
  `Radius` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Address` (`Address`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `FullName` (`FullName`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Users`
#

CREATE TABLE `Users` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  `AccountID` int(10) unsigned NOT NULL default '0',
  `Password` varchar(255) NOT NULL default '',
  `DBPassword` varchar(255) NOT NULL default '',
  `PasswordChanged` datetime NOT NULL default '0000-00-00 00:00:00',
  `RealName` varchar(255) NOT NULL default '',
  `Expires` date NOT NULL default '0000-00-00',
  `Enabled` tinyint(1) NOT NULL default '1',
  `Hosting` tinyint(1) NOT NULL default '1',
  `Admin` tinyint(1) NOT NULL default '0',
  `DB` tinyint(1) NOT NULL default '0',
  `Notes` text NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `AccountID` (`AccountID`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `WebsiteHosts`
#

CREATE TABLE `WebsiteHosts` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `WebsiteID` int(10) unsigned NOT NULL default '0',
  `Host` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Host` (`Host`),
  KEY `WebsiteID` (`WebsiteID`)
) TYPE=MyISAM;

#
# Table structure for table `Websites`
#

CREATE TABLE `Websites` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `UserID` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `Logging` tinyint(1) NOT NULL default '1',
  `Redirect` varchar(255) NOT NULL default '',
  `Parameters` text NOT NULL,
  `ServerID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`),
  KEY `UserID` (`UserID`),
  KEY `ServerID` (`ServerID`)
) TYPE=MyISAM;
# --------------------------------------------------------


#
# Default admin user
#

INSERT INTO Users VALUES (1, 'admin', 300, '$1$94$ojVgVQb5zk3wVJ5bY8emK/', '445762203765b05e', Now(), 'DaeMan Administrator', '0000-00-00', 1, 0, 1, 0, '');

