#
# Table structure for table `Accounts`
#

CREATE TABLE Accounts (
  ID int(10) unsigned NOT NULL auto_increment,
  UserID int(10) unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  AccountID int(10) unsigned NOT NULL default '0',
  Password varchar(255) NOT NULL default '',
  RealName varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY UserID (AccountID),
  UNIQUE KEY UserKey (UserID,Name),
  KEY CustomerID (UserID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Aliases`
#

CREATE TABLE Aliases (
  ID int(10) unsigned NOT NULL auto_increment,
  DomainID int(10) unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  Type enum('account','address','sms') NOT NULL default 'account',
  Data varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY UserKey (DomainID,Name),
  KEY DomainID (DomainID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Domains`
#

CREATE TABLE Domains (
  ID int(10) unsigned NOT NULL auto_increment,
  UserID int(10) unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  DomainName varchar(255) NOT NULL default '',
  Registrar varchar(255) NOT NULL default '',
  Expiry date NOT NULL default '0000-00-00',
  Enabled tinyint(1) NOT NULL default '0',
  Mail enum('primary','secondary','none') NOT NULL default 'primary',
  MailPrimary varchar(255) NOT NULL default '',
  DNS enum('primary','secondary','none') NOT NULL default 'primary',
  DNSPrimary varchar(255) NOT NULL default '',
  DNSSerial varchar(10) NOT NULL default '',
  ServerID int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  UNIQUE KEY DomainName (DomainName),
  UNIQUE KEY Name (Name),
  KEY CustomerID (UserID),
  KEY ServerID (ServerID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Hosts`
#

CREATE TABLE Hosts (
  ID int(10) unsigned NOT NULL auto_increment,
  DomainID int(10) unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  Type set('website','a','cname','subdomain') NOT NULL default 'a',
  Data varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY DomainID (DomainID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Servers`
#

CREATE TABLE Servers (
  ID int(10) unsigned NOT NULL auto_increment,
  Name varchar(255) NOT NULL default '',
  Address varchar(15) NOT NULL default '',
  DNS tinyint(1) NOT NULL default '0',
  Mail tinyint(1) NOT NULL default '0',
  HTTP tinyint(1) NOT NULL default '0',
  DB tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (ID),
  UNIQUE KEY Name (Name,Address)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Users`
#

CREATE TABLE Users (
  ID int(10) unsigned NOT NULL auto_increment,
  Name varchar(255) NOT NULL default '',
  AccountID int(10) unsigned NOT NULL default '0',
  Password varchar(255) NOT NULL default '',
  DBPassword varchar(255) NOT NULL default '',
  RealName varchar(255) NOT NULL default '',
  Expires date NOT NULL default '0000-00-00',
  Enabled tinyint(1) NOT NULL default '1',
  Hosting tinyint(1) NOT NULL default '1',
  Admin tinyint(1) NOT NULL default '0',
  DB tinyint(1) NOT NULL default '0',
  Notes text NOT NULL,
  PRIMARY KEY  (ID),
  UNIQUE KEY Name (Name),
  UNIQUE KEY UserID (AccountID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `WebsiteHosts`
#

CREATE TABLE WebsiteHosts (
  ID int(10) unsigned NOT NULL auto_increment,
  WebsiteID int(10) unsigned NOT NULL default '0',
  Host varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID),
  UNIQUE KEY Host (Host),
  KEY WebsiteID (WebsiteID)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `Websites`
#

CREATE TABLE Websites (
  ID int(10) unsigned NOT NULL auto_increment,
  UserID int(10) unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  Trial tinyint(1) NOT NULL default '1',
  Logging tinyint(1) NOT NULL default '1',
  ServerID int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  UNIQUE KEY Name (Name),
  KEY CustomerID (UserID),
  KEY ServerID (ServerID)
) TYPE=MyISAM;

