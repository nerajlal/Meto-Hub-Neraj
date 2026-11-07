git status
git add <files-you-intend-to-deploy>
git diff --cached --stat
git commit -m "Describe the change"
./deploy.sh test --dry-run
./deploy.sh test --push



./deploy.sh status test



./deploy.sh prod --dry-run
./deploy.sh prod



./deploy.sh status prod