# Branch Merge Procedure for WooCommerce Stock Manager

## Overview
This document describes the standard procedure for merging feature branches into the `nightly` branch for active development.

---

## Branch Strategy

- **`main`** - Stable releases only (production-ready)
- **`nightly`** - Active development (default branch, protected)
- **`claude/*`** - Feature/audit branches (temporary, for specific work)
- **`feature/*`** - Other feature branches

---

## Standard Merge Procedure

### Prerequisites
✅ Feature branch is complete and tested
✅ All commits are pushed to remote
✅ Working directory is clean (`git status`)
✅ You have appropriate GitHub permissions

### Step 1: Fetch Latest Changes
```bash
# Fetch all remote branches
git fetch --all

# Check which commits will be merged
git log origin/nightly..your-feature-branch --oneline
```

### Step 2: Checkout Nightly Branch
```bash
# Create local nightly tracking remote
git checkout -b nightly origin/nightly

# Or if already exists
git checkout nightly
git pull origin nightly
```

### Step 3: Verify Current State
```bash
# Check current branch
git branch

# Verify clean working tree
git status

# See recent commits on nightly
git log --oneline -5
```

### Step 4: Perform Merge
```bash
# Merge feature branch with no fast-forward (preserves history)
git merge feature-branch-name --no-ff -m "Merge feature: Description

Detailed description of what this merge includes.
- Key feature 1
- Key feature 2
- Key feature 3

Co-authored-by: Name <email@example.com>"
```

### Step 5: Resolve Conflicts (if any)
```bash
# Check conflicted files
git status

# Edit conflicted files manually
# Look for <<<<<<< HEAD markers

# After resolving, mark as resolved
git add path/to/resolved/file

# Continue merge
git commit
```

### Step 6: Verify Merge
```bash
# View merge commit
git log --graph --oneline -10

# Check file changes
git show HEAD --stat

# Test locally before pushing
npm test  # or appropriate test command
```

### Step 7A: Push to Remote (If No Branch Protection)
```bash
# Push merged nightly to remote
git push origin nightly

# Verify on GitHub
# Visit: https://github.com/owner/repo/tree/nightly
```

### Step 7B: Create Pull Request (If Branch Protected) ✅ RECOMMENDED
```bash
# If direct push fails with 403 error, create PR instead

# Option 1: Via GitHub URL
# Visit: https://github.com/owner/repo/compare/nightly...feature-branch

# Option 2: Via GitHub CLI (if installed)
gh pr create \
  --base nightly \
  --head feature-branch \
  --title "Merge: Feature Description" \
  --body "Detailed PR description"

# Option 3: Manually via GitHub UI
# 1. Go to repository on GitHub
# 2. Click "Pull requests" tab
# 3. Click "New pull request"
# 4. Set base: nightly, compare: feature-branch
# 5. Create and merge PR
```

---

## Quick Reference Commands

### Common Scenarios

#### Scenario 1: Simple Merge (No Conflicts)
```bash
git fetch --all
git checkout nightly
git pull origin nightly
git merge feature-branch --no-ff
git push origin nightly
```

#### Scenario 2: Merge with Branch Protection
```bash
# Feature branch already pushed to remote
# Visit GitHub and create PR
# URL: https://github.com/owner/repo/compare/nightly...feature-branch
```

#### Scenario 3: Abort Failed Merge
```bash
git merge --abort
git reset --hard origin/nightly
```

#### Scenario 4: Undo Last Commit (Before Push)
```bash
git reset --soft HEAD~1
```

#### Scenario 5: Force Rollback (After Push - DANGEROUS)
```bash
# ONLY if you have admin rights and it's critical
git reset --hard origin/nightly
git push origin nightly --force
# WARNING: This deletes commits permanently
```

---

## Best Practices

### ✅ DO
- Use `--no-ff` flag to preserve merge history
- Write descriptive merge commit messages
- Include co-author credits in commits
- Test locally before pushing
- Create PR for protected branches
- Review diff before merging (`git diff nightly..feature`)
- Keep feature branches focused and small
- Delete feature branches after merging

### ❌ DON'T
- Force push to nightly (`--force`) unless emergency
- Merge directly without reviewing changes
- Commit with failing tests
- Merge incomplete features
- Bypass branch protection rules
- Rebase public branches
- Mix multiple unrelated features in one merge

---

## Merge Commit Message Template

```
Merge feature: Brief one-line description

Detailed description of what this merge includes:
- Major change 1 with explanation
- Major change 2 with explanation
- Major change 3 with explanation

Technical Details:
- Files changed: X files
- Lines added: Y
- Lines removed: Z

Testing:
- [x] Unit tests passing
- [x] Manual testing complete
- [x] No breaking changes

Related Issues:
- Fixes #123
- Closes #456

Co-authored-by: Name <email@example.com>
Co-authored-by: Claude <code@claude.ai>
```

---

## Troubleshooting

### Problem: Push returns "403 Forbidden"
**Cause:** Branch protection enabled on nightly
**Solution:** Create pull request instead of direct push

### Problem: "Merge conflict" errors
**Cause:** Same file modified in both branches
**Solution:**
```bash
# Check conflicted files
git status

# Edit files to resolve conflicts
# Remove <<<<<<, =======, >>>>>>> markers

# Mark as resolved
git add conflicted-file.php

# Complete merge
git commit
```

### Problem: "Already up to date" but commits missing
**Cause:** Feature branch not based on latest nightly
**Solution:**
```bash
git checkout feature-branch
git rebase nightly
git checkout nightly
git merge feature-branch --no-ff
```

### Problem: Merged wrong branch by mistake
**Solution (Before Push):**
```bash
git reset --hard origin/nightly
```

**Solution (After Push - requires admin):**
```bash
git revert -m 1 HEAD
git push origin nightly
```

---

## Branch Protection Settings

Recommended settings for `nightly` branch:

- ✅ Require pull request before merging
- ✅ Require approvals: 1
- ✅ Dismiss stale approvals
- ✅ Require status checks to pass
- ✅ Require branches to be up to date
- ✅ Include administrators
- ❌ Allow force pushes
- ❌ Allow deletions

To configure: https://github.com/owner/repo/settings/branches

---

## Post-Merge Checklist

After successful merge to nightly:

- [ ] Verify all commits appear on GitHub nightly branch
- [ ] Run full test suite
- [ ] Check CI/CD pipeline status
- [ ] Update project board/issues if applicable
- [ ] Notify team members of significant changes
- [ ] Delete merged feature branch (locally and remote)
- [ ] Update documentation if needed
- [ ] Tag release if ready for main branch

---

## Emergency Rollback

If a merge causes critical issues in nightly:

```bash
# Find the merge commit hash
git log --oneline -10

# Revert the merge (creates new commit)
git revert -m 1 <merge-commit-hash>
git push origin nightly

# Or hard reset (DANGEROUS - requires force push)
git reset --hard <commit-before-merge>
git push origin nightly --force
```

---

## Additional Resources

- Git Documentation: https://git-scm.com/doc
- GitHub Flow: https://guides.github.com/introduction/flow/
- Branch Protection: https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches

---

## Document Maintenance

**Last Updated:** 2025-11-18
**Maintained By:** Ojārs Kapteinis
**Review Schedule:** Quarterly or when workflow changes

---

## License

This document is part of the WooCommerce Stock Manager project.
Licensed under GPLv2 - see LICENSE file for details.
