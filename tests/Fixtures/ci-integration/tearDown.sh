cd $( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

git checkout master -q
rm -rf .git
