git checkout master
git merge wip
git push --all
git checkout production
git merge wip
git push --all
git checkout wip

ssh alexseif.com@s207080.gridserver.com <<<EOF
cd domains/myapp.alexseif.com
./deployer.sh
EOF