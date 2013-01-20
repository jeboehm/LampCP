CREATE VIEW postfix_virtual_aliases AS
(
  SELECT MailForward.id, MailForward.domain_id, CONCAT(MailAddress.address, '@', Domain.domain)
    AS source, MailForward.target AS destination
  FROM MailForward, MailAddress, Domain
  WHERE MailForward.domain_id = Domain.id
    AND MailForward.mailaddress_id = MailAddress.id
) UNION (
  SELECT MailAccount.id, MailAccount.domain_id, CONCAT(MailAddress.address, '@', Domain.domain)
    AS source, CONCAT(MailAddress.address, '@', Domain.domain)
    AS destination FROM MailAddress, Domain, MailAccount
  WHERE MailAddress.domain_id = Domain.id
    AND MailAccount.mailaddress_id = MailAddress.id
    AND MailAccount.enabled = 1
    AND MailAccount.password != ''
)
