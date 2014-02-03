CREATE TABLE IF NOT EXISTS `#__eb` (
  `id` integer NOT NULL auto_increment,
  `api_key` varchar(255) default '',
  `ask_for_first_name` boolean default TRUE,
  `first_name_label` varchar(255) default 'Your First Name',
  `ask_for_last_name` boolean default TRUE,
  `last_name_label` varchar(255) default 'Your Last Name',
  `email_label` varchar(255)  default 'Your Email',
  `ask_for_phone` boolean default TRUE,
  `phone_label` varchar(255)  default 'Your Phone',
  `welcome_message` varchar(255)  default 'Subscribe here, will keep your information private',
  `thanks_message` varchar(255)  default 'Thanks for submitting.',
  `connect_with_campaign_id` integer default 0,
  `add_to_group_id` integer default 0,
  PRIMARY KEY  (`id`),
  KEY `idx_api_key` (`api_key`)
)  DEFAULT CHARSET=utf8;

INSERT INTO `#__eb` (`first_name_label`) VALUES ('Your First Name');

