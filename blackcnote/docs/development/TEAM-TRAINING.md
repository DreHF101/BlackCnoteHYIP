# BlackCnote Team Training Guide

## Overview

This guide provides comprehensive training for your team on the new BlackCnote infrastructure, monitoring systems, and operational procedures.

## 🎯 Training Objectives

By the end of this training, team members will be able to:
- Understand the new infrastructure architecture
- Monitor system performance and health
- Respond to alerts and incidents
- Deploy code safely to production
- Troubleshoot common issues
- Use the performance dashboard effectively

---

## 📚 Module 1: Infrastructure Overview

### 1.1 System Architecture

**Load Balancing Layer**
- **Nginx Load Balancer**: Distributes traffic across 3 backend servers
- **Sticky Sessions**: User sessions maintained on same server
- **Rate Limiting**: Prevents abuse and DDoS attacks
- **SSL Termination**: Handles HTTPS encryption

**Application Layer**
- **3 PHP-FPM Pools**: Independent application instances
- **Ports**: 8001, 8002, 8003 (weighted 3:3:2)
- **Auto-scaling**: Dynamic process management
- **Health Checks**: Continuous monitoring

**Database Layer**
- **Sharded Architecture**: 3 database instances
- **Shard 0**: User data (users, sessions, meta)
- **Shard 1**: Transaction data (deposits, withdrawals)
- **Shard 2**: Content data (posts, pages, options)
- **Connection Pooling**: Optimized database access

**Caching Layer**
- **Redis**: Session storage and data caching
- **OPcache**: PHP bytecode caching
- **Nginx Cache**: Static asset caching
- **Browser Cache**: Client-side caching

### 1.2 Key Components

```bash
# Core Services
nginx          # Load balancer and web server
mysql          # Database (3 instances)
redis          # Caching and sessions
php8.1-fpm     # PHP application server
fail2ban       # Security and intrusion prevention
ufw            # Firewall

# Monitoring Services
blackcnote-monitor  # Performance monitoring
logrotate          # Log management
supervisor         # Process management
```

---

## 📊 Module 2: Performance Monitoring Dashboard

### 2.1 Accessing the Dashboard

**URL**: `https://your-domain.com/wp-admin/admin.php?page=performance-dashboard`

**Access Levels**:
- **Admin**: Full access to all metrics
- **Developer**: Read-only access to performance data
- **Support**: Limited access to health status

### 2.2 Key Metrics Explained

#### Response Time Metrics
- **Average**: Mean response time across all requests
- **P50**: 50% of requests complete within this time
- **P95**: 95% of requests complete within this time
- **P99**: 99% of requests complete within this time

#### Memory Usage
- **Current**: Real-time memory consumption
- **Peak**: Highest memory usage recorded
- **Percentage**: Memory usage relative to limit

#### Error Rates
- **Total Errors**: Count of failed requests
- **Error Rate**: Percentage of failed requests
- **Error Types**: Breakdown by error category

#### Database Performance
- **Query Count**: Total database queries
- **Slow Queries**: Queries exceeding threshold
- **Average Query Time**: Mean query execution time

#### Cache Performance
- **Hit Rate**: Percentage of cache hits
- **Hits**: Successful cache retrievals
- **Misses**: Cache misses requiring database queries

### 2.3 Reading the Charts

#### Response Time Distribution
- **Green Zone**: Optimal performance (< 500ms)
- **Yellow Zone**: Acceptable performance (500ms - 2s)
- **Red Zone**: Poor performance (> 2s)

#### Memory Usage Trend
- **Current Usage**: Real-time memory consumption
- **Peak Usage**: Historical maximum
- **Available**: Remaining memory capacity

### 2.4 Alert Management

#### Alert Severity Levels
- **Info**: Informational messages
- **Warning**: Performance degradation
- **Critical**: System issues requiring immediate attention

#### Alert Actions
1. **Acknowledge**: Mark alert as reviewed
2. **Investigate**: Begin troubleshooting
3. **Escalate**: Contact senior team member
4. **Resolve**: Mark issue as fixed

---

## 🚨 Module 3: Alert Response Procedures

### 3.1 Alert Types and Responses

#### High Response Time Alerts
**Symptoms**: Response time > 2 seconds
**Immediate Actions**:
1. Check server load (`htop`)
2. Review recent deployments
3. Check database performance
4. Verify cache status

**Escalation**: If unresolved in 15 minutes

#### High Memory Usage Alerts
**Symptoms**: Memory usage > 80%
**Immediate Actions**:
1. Check for memory leaks
2. Review active processes
3. Restart PHP-FPM if necessary
4. Check for infinite loops

**Escalation**: If memory usage > 90%

#### High Error Rate Alerts
**Symptoms**: Error rate > 5%
**Immediate Actions**:
1. Check error logs
2. Review recent code changes
3. Check database connectivity
4. Verify external API status

**Escalation**: If error rate > 10%

#### Database Performance Alerts
**Symptoms**: Slow queries or connection issues
**Immediate Actions**:
1. Check database load
2. Review slow query log
3. Check connection pool status
4. Verify shard health

**Escalation**: If database unavailable

### 3.2 Incident Response Workflow

#### Step 1: Alert Reception
1. **Acknowledge** the alert immediately
2. **Assess** severity and impact
3. **Notify** relevant team members
4. **Begin** investigation

#### Step 2: Investigation
1. **Gather** information from monitoring tools
2. **Check** recent changes and deployments
3. **Review** logs for error patterns
4. **Identify** root cause

#### Step 3: Resolution
1. **Implement** immediate fix if possible
2. **Test** the solution
3. **Monitor** for improvement
4. **Document** the incident

#### Step 4: Post-Incident
1. **Review** incident timeline
2. **Update** runbooks and procedures
3. **Implement** preventive measures
4. **Schedule** team debrief

---

## 🚀 Module 4: Deployment Procedures

### 4.1 Pre-Deployment Checklist

#### Code Review
- [ ] All tests passing
- [ ] Code review completed
- [ ] Security scan clean
- [ ] Performance impact assessed

#### Environment Preparation
- [ ] Staging environment tested
- [ ] Database migrations ready
- [ ] Configuration files updated
- [ ] Backup completed

#### Team Coordination
- [ ] Deployment window scheduled
- [ ] Team members notified
- [ ] Rollback plan prepared
- [ ] Monitoring alerts configured

### 4.2 Deployment Process

#### Automated Deployment (Recommended)
```bash
# Trigger deployment via GitHub Actions
git push origin main

# Monitor deployment progress
# Check GitHub Actions dashboard
# Verify health checks pass
```

#### Manual Deployment (Emergency)
```bash
# Run deployment script
./scripts/deploy-production.sh

# Monitor deployment
tail -f /var/log/blackcnote/deploy_*.log

# Verify deployment
curl https://your-domain.com/health
```

### 4.3 Post-Deployment Verification

#### Health Checks
1. **Website Accessibility**: Test main pages
2. **API Endpoints**: Verify API functionality
3. **Database Connectivity**: Check all shards
4. **Cache Functionality**: Verify Redis operations

#### Performance Monitoring
1. **Response Times**: Monitor for degradation
2. **Error Rates**: Check for new errors
3. **Memory Usage**: Watch for memory leaks
4. **Database Performance**: Monitor query times

#### User Impact Assessment
1. **User Feedback**: Monitor support tickets
2. **Analytics**: Check user behavior changes
3. **Error Reports**: Review error tracking
4. **Performance Metrics**: Compare before/after

### 4.4 Rollback Procedures

#### Quick Rollback
```bash
# Revert to previous version
git revert HEAD
git push origin main

# Or restore from backup
./scripts/restore-backup.sh latest
```

#### Emergency Rollback
```bash
# Stop current deployment
systemctl stop blackcnote

# Restore previous version
tar -xzf /var/backups/blackcnote/backup_*.tar.gz -C /

# Restart services
systemctl start blackcnote
systemctl start nginx
systemctl start mysql
systemctl start redis
```

---

## 🔧 Module 5: Troubleshooting Guide

### 5.1 Common Issues and Solutions

#### High Response Times
**Diagnosis**:
```bash
# Check server load
htop

# Check PHP-FPM status
systemctl status php8.1-fpm

# Check database connections
mysql -u root -p -e "SHOW PROCESSLIST;"

# Check Redis status
redis-cli ping
```

**Solutions**:
- Restart PHP-FPM: `systemctl restart php8.1-fpm`
- Clear caches: `redis-cli flushall`
- Optimize database queries
- Scale up resources

#### Memory Issues
**Diagnosis**:
```bash
# Check memory usage
free -h

# Check PHP memory usage
php -i | grep memory_limit

# Check for memory leaks
ps aux --sort=-%mem | head -10
```

**Solutions**:
- Increase PHP memory limit
- Restart PHP-FPM
- Investigate memory leaks
- Scale up server resources

#### Database Issues
**Diagnosis**:
```bash
# Check MySQL status
systemctl status mysql

# Check slow queries
tail -f /var/log/mysql/slow.log

# Check connections
mysql -u root -p -e "SHOW STATUS LIKE 'Threads_connected';"
```

**Solutions**:
- Restart MySQL: `systemctl restart mysql`
- Optimize slow queries
- Increase connection limits
- Check shard health

#### Cache Issues
**Diagnosis**:
```bash
# Check Redis status
systemctl status redis

# Test Redis connectivity
redis-cli ping

# Check cache hit rate
# Review performance dashboard
```

**Solutions**:
- Restart Redis: `systemctl restart redis`
- Clear cache: `redis-cli flushall`
- Check Redis memory usage
- Optimize cache configuration

### 5.2 Log Analysis

#### Nginx Logs
```bash
# Access logs
tail -f /var/log/blackcnote/nginx-access.log

# Error logs
tail -f /var/log/blackcnote/nginx-error.log

# Search for errors
grep " 50[0-9] " /var/log/blackcnote/nginx-access.log
```

#### PHP-FPM Logs
```bash
# PHP error logs
tail -f /var/log/blackcnote/php-fpm.log

# PHP-FPM status
systemctl status php8.1-fpm
```

#### MySQL Logs
```bash
# MySQL error log
tail -f /var/log/mysql/error.log

# Slow query log
tail -f /var/log/mysql/slow.log
```

#### Application Logs
```bash
# WordPress debug log
tail -f /var/www/blackcnote/wp-content/debug.log

# BlackCnote logs
tail -f /var/log/blackcnote/*.log
```

### 5.3 Performance Optimization

#### Database Optimization
```sql
-- Check slow queries
SELECT * FROM mysql.slow_log ORDER BY start_time DESC LIMIT 10;

-- Analyze table performance
ANALYZE TABLE wp_posts;

-- Optimize tables
OPTIMIZE TABLE wp_posts;
```

#### Cache Optimization
```bash
# Check cache hit rate
redis-cli info stats | grep hit

# Monitor cache memory
redis-cli info memory

# Clear specific cache keys
redis-cli DEL "cache_key_name"
```

#### PHP Optimization
```bash
# Check OPcache status
php -i | grep opcache

# Clear OPcache
php -r "opcache_reset();"

# Check PHP-FPM configuration
php-fpm8.1 -t
```

---

## 📋 Module 6: Daily Operations

### 6.1 Morning Checklist

#### System Health Review
1. **Check Dashboard**: Review overnight metrics
2. **Review Alerts**: Address any pending alerts
3. **Check Logs**: Review error logs for issues
4. **Verify Backups**: Confirm backup completion

#### Performance Monitoring
1. **Response Times**: Check for degradation
2. **Error Rates**: Monitor for spikes
3. **Resource Usage**: Check memory and CPU
4. **Database Health**: Verify shard status

### 6.2 Weekly Maintenance

#### Performance Review
1. **Trend Analysis**: Review weekly performance trends
2. **Capacity Planning**: Assess resource usage
3. **Optimization**: Identify improvement opportunities
4. **Documentation**: Update runbooks and procedures

#### Security Review
1. **Access Logs**: Review for suspicious activity
2. **Fail2ban Status**: Check blocked IPs
3. **SSL Certificates**: Verify expiration dates
4. **Security Updates**: Apply patches

### 6.3 Monthly Tasks

#### Infrastructure Review
1. **Scaling Assessment**: Evaluate growth needs
2. **Cost Optimization**: Review resource utilization
3. **Backup Testing**: Verify restore procedures
4. **Disaster Recovery**: Test recovery procedures

#### Team Training
1. **Skill Assessment**: Identify training needs
2. **Procedure Updates**: Review and update runbooks
3. **Incident Review**: Learn from past incidents
4. **Best Practices**: Share knowledge and tips

---

## 🎓 Module 7: Advanced Topics

### 7.1 Load Testing

#### Running Load Tests
```bash
# Basic load test
node scripts/load-test.js

# Custom load test
CONCURRENT_USERS=200 TEST_DURATION=600 node scripts/load-test.js

# Stress test
CONCURRENT_USERS=500 TEST_DURATION=300 node scripts/load-test.js
```

#### Interpreting Results
- **Response Times**: Should be < 2s for 95% of requests
- **Error Rates**: Should be < 5%
- **Throughput**: Should handle 100+ req/s
- **Resource Usage**: Monitor for bottlenecks

### 7.2 Database Sharding

#### Shard Management
```php
// Get shard for user
$shard = DatabaseSharding::getInstance()->getShardForUser($userId);

// Execute query on specific shard
$result = DatabaseSharding::getInstance()->executeOnShard($shard, $sql, $params);

// Get shard health
$health = DatabaseSharding::getInstance()->getShardHealth();
```

#### Shard Monitoring
- **Health Status**: Monitor each shard
- **Performance**: Track query times per shard
- **Capacity**: Monitor shard sizes
- **Balancing**: Check data distribution

### 7.3 Performance Tuning

#### Nginx Optimization
```nginx
# Worker processes
worker_processes auto;

# Connection limits
worker_connections 1024;

# Buffer sizes
client_body_buffer_size 128k;
client_header_buffer_size 1k;
```

#### PHP-FPM Optimization
```ini
; Process management
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35

; Memory settings
memory_limit = 256M
max_execution_time = 300
```

#### MySQL Optimization
```ini
[mysqld]
# Buffer pool
innodb_buffer_pool_size = 1G

# Log files
innodb_log_file_size = 256M

# Connections
max_connections = 200
```

---

## 📞 Module 8: Support and Escalation

### 8.1 Support Channels

#### Internal Support
- **Slack**: #blackcnote-support
- **Email**: support@blackcnote.com
- **Phone**: Emergency hotline

#### External Support
- **Hosting Provider**: Server infrastructure
- **Domain Registrar**: DNS issues
- **SSL Provider**: Certificate issues

### 8.2 Escalation Matrix

#### Level 1: Initial Response
- **Response Time**: 15 minutes
- **Team**: Support team
- **Actions**: Basic troubleshooting, alert acknowledgment

#### Level 2: Technical Escalation
- **Response Time**: 30 minutes
- **Team**: Development team
- **Actions**: Code review, performance analysis

#### Level 3: Management Escalation
- **Response Time**: 1 hour
- **Team**: Technical lead, DevOps
- **Actions**: Infrastructure changes, major decisions

#### Level 4: Executive Escalation
- **Response Time**: 2 hours
- **Team**: CTO, CEO
- **Actions**: Business impact assessment, resource allocation

### 8.3 Communication Procedures

#### Incident Communication
1. **Initial Alert**: Immediate notification to team
2. **Status Updates**: Regular updates every 30 minutes
3. **Resolution**: Final status update
4. **Post-Incident**: Detailed report within 24 hours

#### Stakeholder Communication
- **Users**: Status page updates
- **Management**: Executive summary
- **Team**: Technical details
- **External**: Public relations if needed

---

## 📝 Module 9: Documentation and Knowledge Management

### 9.1 Runbook Maintenance

#### Updating Procedures
1. **Review Frequency**: Monthly review of all procedures
2. **Update Triggers**: After incidents or system changes
3. **Version Control**: Track changes and versions
4. **Team Review**: Ensure accuracy and completeness

#### Knowledge Sharing
- **Team Meetings**: Regular knowledge sharing sessions
- **Documentation**: Keep procedures up to date
- **Training**: Regular team training sessions
- **Lessons Learned**: Document from incidents

### 9.2 Best Practices

#### Monitoring Best Practices
- **Set Realistic Thresholds**: Avoid alert fatigue
- **Use Multiple Metrics**: Don't rely on single indicators
- **Regular Review**: Update thresholds based on trends
- **Documentation**: Keep monitoring procedures current

#### Deployment Best Practices
- **Test Everything**: Never deploy untested code
- **Rollback Plan**: Always have a rollback strategy
- **Gradual Rollout**: Use feature flags when possible
- **Monitor Closely**: Watch metrics during deployment

#### Security Best Practices
- **Principle of Least Privilege**: Minimal access rights
- **Regular Updates**: Keep systems patched
- **Access Monitoring**: Track all system access
- **Incident Response**: Have security incident procedures

---

## 🎯 Module 10: Assessment and Certification

### 10.1 Training Assessment

#### Knowledge Check
- **Infrastructure Understanding**: 80% minimum score
- **Monitoring Proficiency**: 90% minimum score
- **Troubleshooting Skills**: 85% minimum score
- **Deployment Procedures**: 95% minimum score

#### Practical Assessment
- **Dashboard Navigation**: Demonstrate proficiency
- **Alert Response**: Simulate incident response
- **Deployment Process**: Execute safe deployment
- **Troubleshooting**: Resolve common issues

### 10.2 Certification Levels

#### Level 1: Basic Operator
- **Requirements**: Complete modules 1-3
- **Responsibilities**: Monitor dashboard, acknowledge alerts
- **Escalation**: Escalate issues to Level 2

#### Level 2: Technical Operator
- **Requirements**: Complete modules 1-6
- **Responsibilities**: Troubleshoot issues, perform deployments
- **Escalation**: Escalate complex issues to Level 3

#### Level 3: Senior Operator
- **Requirements**: Complete all modules
- **Responsibilities**: Lead incident response, optimize systems
- **Escalation**: Escalate business-impacting issues to management

### 10.3 Ongoing Training

#### Refresher Training
- **Frequency**: Quarterly refresher sessions
- **Content**: Updated procedures, new features
- **Assessment**: Regular competency checks
- **Certification**: Annual recertification

#### Advanced Training
- **Topics**: New technologies, advanced troubleshooting
- **Format**: Workshops, hands-on labs
- **Instructors**: Senior team members, external experts
- **Certification**: Advanced level certification

---

## 📚 Resources and References

### Documentation
- **Infrastructure Docs**: `/docs/PHASE-4-SCALABILITY-COMPLETION-SUMMARY.md`
- **API Documentation**: `/docs/API.md`
- **Deployment Guide**: `/scripts/README.md`
- **Monitoring Config**: `/config/monitoring-thresholds.json`

### Tools and Scripts
- **Load Testing**: `/scripts/load-test.js`
- **Deployment**: `/scripts/deploy-production.sh`
- **Setup**: `/scripts/setup-production.sh`
- **Monitoring**: Performance dashboard

### External Resources
- **Nginx Documentation**: https://nginx.org/en/docs/
- **PHP-FPM Documentation**: https://www.php.net/manual/en/install.fpm.php
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **Redis Documentation**: https://redis.io/documentation

### Support Contacts
- **Technical Lead**: [Contact Information]
- **DevOps Team**: [Contact Information]
- **Infrastructure Provider**: [Contact Information]
- **Emergency Hotline**: [Contact Information]

---

## 🎉 Conclusion

This training guide provides comprehensive coverage of the BlackCnote infrastructure and operational procedures. Regular review and updates ensure the team remains proficient and the system operates optimally.

**Remember**: The key to successful operations is proactive monitoring, quick response to issues, and continuous learning from experiences.

**Next Steps**:
1. Complete all training modules
2. Take the assessment tests
3. Participate in hands-on exercises
4. Get certified for your level
5. Begin regular operational duties

**Questions and Support**: Contact the training team for any questions or additional support. 