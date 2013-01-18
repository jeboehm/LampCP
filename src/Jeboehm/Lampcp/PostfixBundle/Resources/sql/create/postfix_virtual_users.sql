CREATE VIEW postfix_virtual_users AS
  SELECT MailAccount.id, MailAccount.domain_id, CONCAT(MailAddress.address, '@', Domain.domain)
    AS email, MailAccount.password
  FROM MailAccount, MailAddress, Domain
  WHERE MailAccount.domain_id = Domain.id
    AND MailAccount.mailaddress_id = MailAddress.id
    AND MailAccount.enabled = 1
