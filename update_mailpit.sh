#!/bin/bash
# Update .env for Mailpit
sed -i 's/^MAIL_MAILER=.*/MAIL_MAILER=smtp/' .env
sed -i 's/^MAIL_HOST=.*/MAIL_HOST=mailpit/' .env
sed -i 's/^MAIL_PORT=.*/MAIL_PORT=1025/' .env
sed -i 's/^MAIL_USERNAME=.*/MAIL_USERNAME=null/' .env
sed -i 's/^MAIL_PASSWORD=.*/MAIL_PASSWORD=null/' .env
sed -i 's/^MAIL_ENCRYPTION=.*/MAIL_ENCRYPTION=null/' .env

echo "✅ Updated .env for Mailpit"
echo ""
echo "Current mail settings:"
grep "^MAIL_" .env
