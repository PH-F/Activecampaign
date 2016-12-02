#Package to connect to the ActiveCampaign 5 api


$Mailinglist = new ActivecampaignManager('url','user','pass');
 
 
### get a list (id,name) of all mailinglists in Active Campaign
 $Mailinglist->lists(); 
 
### publish a mailing to Active Campaign
$Mailinglist->publish('Subject','From-email','From-name','Body <html>','websiteUrl','mailing list id'));

  