CREATE VIEW postfix_virtual_domains AS
  SELECT Domain.id, Domain.domain
  FROM Domain, MailAddress
  WHERE MailAddress.domain_id = Domain.id
  GROUP BY Domain.id
