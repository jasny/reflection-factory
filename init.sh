REPO_NAME=$(pwd | xargs basename)

OPEN=$(command -v xdg-open && echo 'xdg-open' || echo 'open')

SCRUTINIZER_ORGANIZATION=jasny
SCRUTINIZER_GLOBAL_CONFIG=9fc4e5aa-b4a6-4b2b-b698-9a17549e1ddc

echo -n "Repository description: " && read REPO_DESCRIPTION

mv README.md.dist README.md
sed -i 's~{{library}}~'$REPO_NAME'~' README.md
sed -i 's~{{name}}~'$(echo $REPO_NAME | sed -r 's/-/ /g')'~' README.md
sed -i 's~{{description}}~'$REPO_DESCRIPTION'~' README.md
sed -i 's~jasny/library~jasny/'$REPO_NAME'~' composer.json
sed -i 's~Jasny\\Library~Jasny\\'$(echo $REPO_NAME | sed -r 's/(^|-)(\w)/\U\2/g')'~' composer.json

mkdir -p src tests
composer install

cp vendor/jasny/php-code-quality/phpunit.xml.dist .
cp vendor/jasny/php-code-quality/phpcs.xml.dist ./phpcs.xml
cp vendor/jasny/php-code-quality/phpstan.neon.dist ./phpstan.neon
cp vendor/jasny/php-code-quality/travis.yml.dist ./.travis.yml
cp vendor/jasny/php-code-quality/bettercodehub.yml.dist ./.bettercodehub.yml

git add .
git commit -a -m "Initial commit"
git remote show origin 2> /dev/null || hub create -d "$REPO_DESCRIPTION"
git push -u origin master

# Travis
travis sync && travis enable

# Scrutinizer
if [ -n "$SCRUTINIZER_ACCESS_TOKEN" ] ; then
  echo "Skipping scrutinizer: access token not configured"
else
  curl --header "Content-Type: application/json" --request POST \
    --data "{\"name\":\"jasny/$REPO_NAME\",\"organization\":\"$SCRUTINIZER_ORGANIZATION\",\"global_config\"=\"$SCRUTINIZER_GLOBAL_CONFIG\"}" \
    https://scrutinizer-ci.com/api/repositories/g?access_token="$SCRUTINIZER_ACCESS_TOKEN"
fi

# TODO use sensiolabs API or CLI
$OPEN https://insight.sensiolabs.com/projects/new/github

# Better code hub doesn't have an API
echo "Enable BetterCodeHub via https://bettercodehub.com/repositories"

# Edit README
$OPEN README.md

