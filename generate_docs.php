<?php
exec('doxygen');

exec('wkhtmltopdf ./html/index.html ./docs/documentation.pdf');

echo "Documentation PDF générée avec succès !";