#!/bin/bash

DIR=/app

##############

cd $DIR

echo "Getting file from modx.com..."
wget -O modx.zip http://modx.com/download/latest/
echo "Unzipping file..."
unzip "./modx.zip" -d ./

ZDIR=`ls -F | grep "\/" | head -1`
if [ "${ZDIR}" = "/" ]; then
        echo "Failed to find directory..."; exit
fi

if [ -d "${ZDIR}" ]; then
        cd ${ZDIR}
        echo "Moving out of temp dir..."
        mv ./* ../
        cd ../
        rm -rf "./${ZDIR}"

        echo "Removing zip file..."
        rm "./modx.zip"

        cd "setup"
        chmod +x index.php
        echo "Running setup..."
        php index.php --installmode=new --config=config.xml
                
        cd ..

        echo "Install packages"        
        php package.php 
        
        echo "Install structure"        
        php init_structure.php  
        
        #echo "Copy fix script"
        #cp /home/petun/scripts/fix.php ./        

        echo "Done!"
else
        echo "Failed to find directory: ${ZDIR}"
        exit
fi


mv ht.access .htaccess

echo "Reset password for petun account"
mysql -u modx -h db -p -e "USE \`modx\`; UPDATE modx_users SET hash_class = 'hashing.modMD5', password = MD5('12345678') WHERE username = 'petun';"

echo "configure friendly URLs and sessionClass"
mysql -u modx -h db -p -e "USE \`modx\`; UPDATE \`modx_system_settings\` SET \`value\` = 1 WHERE \`key\` = 'automatic_alias'; UPDATE \`modx_system_settings\` SET \`value\` ='russian' WHERE  \`key\` = 'friendly_alias_translit'; UPDATE \`modx_system_settings\` SET \`value\` = 1 WHERE \`key\` = 'friendly_urls'; UPDATE \`modx_system_settings\` SET \`value\` = 1 WHERE \`key\` = 'use_alias_path'; UPDATE \`modx_system_settings\` SET \`value\` = '' WHERE \`key\` = 'session_handler_class';"


echo "Rewrite config.inc.php to unique file"
echo "<?php define('MODX_CORE_PATH', dirname(__FILE__).'/core/'); define('MODX_CONFIG_KEY', 'config');" > config.core.php
echo "<?php define('MODX_CORE_PATH', dirname(dirname(__FILE__)).'/core/'); define('MODX_CONFIG_KEY', 'config');" > connectors/config.core.php
echo "<?php define('MODX_CORE_PATH', dirname(dirname(__FILE__)).'/core/'); define('MODX_CONFIG_KEY', 'config');" > manager/config.core.php

echo "Done. Please visit http://localhost:<port>/manager/ to login."
echo "User: 	petun"
echo "Password: 12345678"
echo "Don't forget to register site on apache"
