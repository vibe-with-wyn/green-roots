# Deployment Guide for InfinityFree

> 🌐 **APPLICATION IS LIVE**: [https://green-roots.is-great.net](https://green-roots.is-great.net)  
> ✅ **Deployment Status**: Successfully deployed

---

## Live Application Details

- **Domain**: https://green-roots.is-great.net
- **Hosting**: InfinityFree Free Hosting
- **Database**: MySQL (if0_40752096_greenroots_db)
- **SSL**: Active and configured

---

## Pre-Deployment Checklist

- [ ] Database credentials updated in `includes/config.php`
- [ ] `.htaccess` file present in root
- [ ] All sensitive data removed from code
- [ ] Database SQL file ready (`database/greenroots_db.sql`)
- [ ] Test locally one final time

## File Upload Order

1. **First Upload** (Core Files)
   - `.htaccess`
   - `index.php`
   - `includes/config.php`

2. **Second Upload** (Application Logic)
   - `views/` folder
   - `services/` folder
   - `validator/` folder
   - `admin/` folder
   - `access/` folder

3. **Third Upload** (Assets)
   - `assets/` folder (CSS, JS, images)

4. **Database**
   - Import `database/greenroots_db.sql` via phpMyAdmin

## File Structure Verification

After upload, verify this structure exists in `htdocs`:

```
htdocs/
├── .htaccess
├── index.php
├── access/
├── admin/
├── assets/
├── database/
├── docs/
├── includes/
├── services/
├── validator/
└── views/
```

## InfinityFree Specific Settings

### cPanel Configuration

1. **SSL/TLS**
   - Enable free SSL certificate
   - Force HTTPS redirect (already in `.htaccess`)

2. **PHP Settings**
   - PHP Version: 8.x (latest available)
   - Extensions needed:
     - PDO
     - PDO_MySQL
     - GD (for image processing)
     - JSON
     - Session

3. **MySQL Database**
   - Database name: `if0_40752096_greenroots_db`
   - Import SQL file via phpMyAdmin
   - Note: Remote MySQL access is disabled on free plan

### Security Configuration

The `.htaccess` file already includes:
- Directory browsing disabled
- Sensitive file protection
- Security headers
- HTTPS redirect

### Performance Optimization

InfinityFree Free Plan Limitations:
- **Hits**: 50,000/day
- **Storage**: 5GB
- **Bandwidth**: Unlimited
- **MySQL**: 400 MB
- **No Cron Jobs** (use external services if needed)

Optimization tips:
- Minimize database queries
- Use browser caching (configured in `.htaccess`)
- Compress images before upload
- Minimize CSS/JS files

## Post-Deployment Testing

### Test Sequence

1. **Homepage Access**
   ```
   https://green-roots.is-great.net
   → Should redirect to login
   ```

2. **User Flows**
   - [ ] User registration
   - [ ] User login
   - [ ] Submit tree planting
   - [ ] View dashboard
   - [ ] Claim rewards
   - [ ] Join events

3. **Validator Flows**
   - [ ] Validator login
   - [ ] View pending submissions
   - [ ] Approve submission
   - [ ] Reject submission
   - [ ] View designated site

4. **Admin Flows**
   - [ ] Admin login
   - [ ] Manage validators
   - [ ] Manage planting sites
   - [ ] Upload assets
   - [ ] View analytics

### Common Issues & Solutions

**Database Connection Failed**
```php
// Solution: Update config.php with correct credentials
$host = 'sql211.infinityfree.com';
$dbname = 'if0_40752096_greenroots_db';
```

**Assets Not Loading**
```apache
# Add to .htaccess if needed
<FilesMatch "\.(jpg|jpeg|png|gif|css|js)$">
    Header set Cache-Control "max-age=2592000, public"
</FilesMatch>
```

**Session Issues**
```php
// Ensure in config.php:
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
```

## Rollback Plan

If deployment fails:

1. **Backup Current State**
   - Download all files from `htdocs`
   - Export database via phpMyAdmin

2. **Restore Previous Version**
   - Delete current files
   - Re-upload previous working version
   - Import previous database backup

3. **Document Issues**
   - Note error messages
   - Check server logs
   - Review recent changes

## Support Resources

- **InfinityFree Forum**: https://forum.infinityfree.com/
- **Documentation**: Check `docs/` folder
- **Database Issues**: Use phpMyAdmin in cPanel
- **File Manager**: Access via cPanel

## Success Criteria

Deployment is successful when:
- [ ] Website loads at `https://green-roots.is-great.net`
- [ ] SSL certificate active (HTTPS working)
- [ ] All user roles can login
- [ ] Database operations work
- [ ] File uploads function correctly
- [ ] No console errors in browser
- [ ] Mobile responsive layout works
- [ ] All assets load properly

---

**Domain**: https://green-roots.is-great.net
**Deployed**: [December 28, 2025]
**Version**: 1.0.0
