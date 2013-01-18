CREATE VIEW postfix_virtual_aliases AS
  SELECT MailForward.id, MailForward.domain_id, CONCAT(MailAddress.address, '@', Domain.domain)
    AS source, MailForward.target AS destination
  FROM MailForward, MailAddress, Domain
  WHERE MailForward.domain_id = Domain.id
    AND MailForward.mailaddress_id = MailAddress.id
