git init -q
git config user.name test
git config user.email test
git add .
git commit -m "init" -q

git checkout -b change
touch dir\ a/file.php
rm -r dir\ c
echo "" >> dir\ b/file\ b.php
echo "echo 1;" >> dir\ b/file\ b.php
git add .
git commit -m "change" -q
