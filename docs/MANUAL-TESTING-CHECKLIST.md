# BlackCnote Theme Manual Testing Checklist

## 🎯 Overview
This checklist covers all manual testing required to verify the BlackCnote theme is fully functional after the pathway fixes and cleanup.

## 📋 Pre-Testing Setup
- [ ] Ensure Docker containers are running
- [ ] Verify WordPress is accessible at http://localhost:8888
- [ ] Confirm phpMyAdmin is accessible at http://localhost:8080

---

## 🔧 WordPress Admin Testing

### 1. Theme Activation
**URL:** http://localhost:8888/wp-admin/themes.php

- [ ] Navigate to Appearance > Themes
- [ ] Verify "BlackCnote" theme is listed
- [ ] Check theme details (Author, Version, Description)
- [ ] Click "Activate" on BlackCnote theme
- [ ] Confirm activation success message
- [ ] Verify BlackCnote is now the active theme

### 2. Admin Menu Verification
**URL:** http://localhost:8888/wp-admin/

- [ ] Check for "BlackCnote" menu in admin sidebar
- [ ] Verify submenu items:
  - [ ] General Settings
  - [ ] Live Editing
  - [ ] Dev Tools
  - [ ] System Status
- [ ] Test each submenu page loads correctly
- [ ] Verify admin CSS/JS loads without errors

### 3. Menu Management
**URL:** http://localhost:8888/wp-admin/nav-menus.php

- [ ] Go to Appearance > Menus
- [ ] Check if "Primary Menu" exists
- [ ] Verify menu items are present:
  - [ ] Home
  - [ ] Investment Plans
  - [ ] Calculator
  - [ ] About Us
  - [ ] Contact
- [ ] Test menu assignment to "Primary" location
- [ ] Check if "Footer Menu" exists

### 4. Page Templates
**URL:** http://localhost:8888/wp-admin/edit.php?post_type=page

- [ ] Verify default pages exist:
  - [ ] Dashboard
  - [ ] Investment Plans
  - [ ] About Us
  - [ ] Contact Us
  - [ ] Services
  - [ ] Privacy Policy
  - [ ] Terms of Service
- [ ] Check each page has correct template assigned
- [ ] Verify page content is not empty

---

## 🌐 Frontend Testing

### 1. Homepage
**URL:** http://localhost:8888/

- [ ] Verify homepage loads without errors
- [ ] Check header displays correctly
- [ ] Verify navigation menu is visible
- [ ] Test responsive design (mobile/tablet)
- [ ] Check footer is present
- [ ] Verify no broken links

### 2. Template Pages
Test each template page:

#### Dashboard Page
**URL:** http://localhost:8888/dashboard/

- [ ] Page loads without errors
- [ ] Dashboard template is applied
- [ ] Investment calculator works
- [ ] Portfolio display functions
- [ ] Charts/graphs render correctly

#### Investment Plans Page
**URL:** http://localhost:8888/plans/

- [ ] Page loads without errors
- [ ] Plans template is applied
- [ ] Investment plans are displayed
- [ ] Plan details are visible
- [ ] "Invest Now" buttons work

#### About Page
**URL:** http://localhost:8888/about/

- [ ] Page loads without errors
- [ ] About template is applied
- [ ] Company information is displayed
- [ ] Team section is present

#### Contact Page
**URL:** http://localhost:8888/contact/

- [ ] Page loads without errors
- [ ] Contact form is present
- [ ] Form validation works
- [ ] Contact information is displayed

### 3. Assets and Styling
- [ ] CSS files load without errors
- [ ] JavaScript files load without errors
- [ ] Images display correctly
- [ ] Bootstrap styling is applied
- [ ] Custom theme styling works
- [ ] Responsive design functions

### 4. Interactive Features
- [ ] Investment calculator functionality
- [ ] Form submissions work
- [ ] AJAX requests function
- [ ] Live editing features (if enabled)
- [ ] Widget functionality

---

## ⚙️ Admin Settings Testing

### 1. BlackCnote Settings
**URL:** http://localhost:8888/wp-admin/admin.php?page=blackcnote-settings

- [ ] Settings page loads
- [ ] All form fields are present
- [ ] Settings save correctly
- [ ] Validation works
- [ ] Reset functionality works

### 2. Live Editing
**URL:** http://localhost:8888/wp-admin/admin.php?page=blackcnote-live-editing

- [ ] Live editing page loads
- [ ] Development tools are accessible
- [ ] File editing works
- [ ] Real-time preview functions

### 3. Development Tools
**URL:** http://localhost:8888/wp-admin/admin.php?page=blackcnote-dev-tools

- [ ] Dev tools page loads
- [ ] Debug information is displayed
- [ ] System status is shown
- [ ] Performance metrics are visible

### 4. System Status
**URL:** http://localhost:8888/wp-admin/admin.php?page=blackcnote-system-status

- [ ] System status page loads
- [ ] All services are listed
- [ ] Health checks pass
- [ ] Error logs are accessible

---

## 🔍 Functionality Testing

### 1. Shortcodes
Test all available shortcodes:

- [ ] `[blackcnote_plans]` - Investment plans display
- [ ] `[blackcnote_dashboard]` - Dashboard widget
- [ ] `[blackcnote_transactions]` - Transaction history

### 2. Widgets
**URL:** http://localhost:8888/wp-admin/widgets.php

- [ ] BlackCnote widgets are available
- [ ] Widgets can be added to sidebars
- [ ] Widget settings save correctly
- [ ] Widgets display on frontend

### 3. Custom Post Types
- [ ] Investment Plans post type exists
- [ ] Custom fields are present
- [ ] Archive pages work
- [ ] Single post pages display correctly

### 4. AJAX Functionality
- [ ] Plan calculations work
- [ ] Form submissions via AJAX
- [ ] Dynamic content loading
- [ ] Error handling

---

## 🐛 Error Testing

### 1. Error Handling
- [ ] Test with invalid form data
- [ ] Check error messages display
- [ ] Verify graceful degradation
- [ ] Test 404 page handling

### 2. Performance
- [ ] Page load times are acceptable
- [ ] No excessive database queries
- [ ] Assets are optimized
- [ ] Caching works correctly

### 3. Security
- [ ] Nonce verification works
- [ ] Input sanitization functions
- [ ] XSS protection is active
- [ ] CSRF protection is enabled

---

## 📱 Responsive Testing

### 1. Mobile Devices
- [ ] Test on mobile viewport
- [ ] Navigation menu works on mobile
- [ ] Forms are mobile-friendly
- [ ] Touch interactions work

### 2. Tablet Devices
- [ ] Test on tablet viewport
- [ ] Layout adapts correctly
- [ ] Content is readable
- [ ] Navigation is accessible

### 3. Desktop
- [ ] Test on desktop viewport
- [ ] Full layout displays correctly
- [ ] Hover effects work
- [ ] All features are accessible

---

## 🔧 Development Tools

### 1. Browser Developer Tools
- [ ] No JavaScript errors in console
- [ ] No CSS errors
- [ ] Network requests complete successfully
- [ ] Performance is acceptable

### 2. WordPress Debug
- [ ] Enable WP_DEBUG in wp-config.php
- [ ] Check debug.log for errors
- [ ] Verify no PHP warnings/errors
- [ ] Test with debug mode enabled

---

## 📊 Final Verification

### 1. Complete Walkthrough
- [ ] Navigate through all pages
- [ ] Test all interactive elements
- [ ] Verify all forms work
- [ ] Check all links are functional

### 2. Cross-Browser Testing
- [ ] Test in Chrome
- [ ] Test in Firefox
- [ ] Test in Safari
- [ ] Test in Edge

### 3. Documentation
- [ ] Update any missing documentation
- [ ] Note any issues found
- [ ] Create bug reports if needed
- [ ] Document successful tests

---

## ✅ Success Criteria

The BlackCnote theme is considered fully functional when:

- [ ] All pages load without errors
- [ ] All templates work correctly
- [ ] All admin features function
- [ ] All interactive elements work
- [ ] Responsive design is functional
- [ ] No critical errors in logs
- [ ] Performance is acceptable
- [ ] Security measures are active

---

## 🚨 Issues to Report

If any of the following issues are found, report them immediately:

- [ ] PHP fatal errors
- [ ] White screen of death
- [ ] Database connection errors
- [ ] Missing critical functionality
- [ ] Security vulnerabilities
- [ ] Performance issues
- [ ] Broken navigation
- [ ] Missing content

---

## 📞 Support

If issues are encountered during testing:

1. Check the browser console for errors
2. Review WordPress debug logs
3. Verify Docker containers are running
4. Check file permissions
5. Review the troubleshooting documentation

**Last Updated:** December 2024  
**Version:** 1.0  
**Status:** ✅ Ready for Testing 