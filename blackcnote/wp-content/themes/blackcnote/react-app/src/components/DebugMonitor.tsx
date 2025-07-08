import React, { useState, useEffect, useCallback } from 'react';
import { AlertTriangle, X, CheckCircle, AlertCircle, Info, RefreshCw } from 'lucide-react';

interface DebugError {
  id: string;
  type: 'error' | 'warning' | 'info' | 'success';
  title: string;
  message: string;
  category: 'wordpress' | 'react' | 'xampp' | 'hyiplab' | 'integration' | 'build' | 'file-watching';
  timestamp: Date;
  resolved: boolean;
  severity: 'critical' | 'high' | 'medium' | 'low';
  solution?: string;
}

interface DebugMonitorProps {
  isDevelopment: boolean;
}

const DebugMonitor: React.FC<DebugMonitorProps> = ({ isDevelopment }) => {
  const [errors, setErrors] = useState<DebugError[]>([]);
  const [isExpanded, setIsExpanded] = useState(false);
  const [lastCheck, setLastCheck] = useState<Date>(new Date());
  const [isChecking, setIsChecking] = useState(false);
  const [collapsed, setCollapsed] = useState(true);

  // Debug monitoring functions
  const checkWordPressIntegration = (): DebugError[] => {
    const issues: DebugError[] = [];
    
    // Check if WordPress settings are properly injected
    if (!window.blackCnoteApiSettings) {
      issues.push({
        id: 'wp-settings-missing',
        type: 'error',
        title: 'WordPress Settings Missing',
        message: 'WordPress API settings not found. Check front-page.php configuration.',
        category: 'wordpress',
        timestamp: new Date(),
        resolved: false,
        severity: 'critical',
        solution: 'Verify that front-page.php properly injects window.blackCnoteApiSettings'
      });
    } else {
      // Check for duplicate settings (WordPress sometimes injects twice)
      const settingsKeys = Object.keys(window.blackCnoteApiSettings);
      if (settingsKeys.length === 0) {
        issues.push({
          id: 'wp-settings-empty',
          type: 'warning',
          title: 'WordPress Settings Empty',
          message: 'WordPress API settings object is empty. Check configuration.',
          category: 'wordpress',
          timestamp: new Date(),
          resolved: false,
          severity: 'medium',
          solution: 'Check WordPress functions.php configuration injection'
        });
      } else {
        // Log successful detection for debugging
        console.log('✅ WordPress API Settings detected:', {
          homeUrl: window.blackCnoteApiSettings.homeUrl,
          apiUrl: window.blackCnoteApiSettings.apiUrl,
          themeActive: window.blackCnoteApiSettings.themeActive,
          pluginActive: window.blackCnoteApiSettings.pluginActive,
          wpHeaderFooterDisabled: window.blackCnoteApiSettings.wpHeaderFooterDisabled
        });
      }
    }

    // Check if theme is active (using optional chaining)
    if (window.blackCnoteApiSettings && window.blackCnoteApiSettings.themeActive === false) {
      issues.push({
        id: 'theme-inactive',
        type: 'warning',
        title: 'BlackCnote Theme Inactive',
        message: 'BlackCnote theme is not active in WordPress admin.',
        category: 'wordpress',
        timestamp: new Date(),
        resolved: false,
        severity: 'high',
        solution: 'Go to WordPress Admin → Appearance → Themes → Activate BlackCnote'
      });
    }

    // Check if plugin is active (using optional chaining)
    if (window.blackCnoteApiSettings && window.blackCnoteApiSettings.pluginActive === false) {
      issues.push({
        id: 'plugin-inactive',
        type: 'warning',
        title: 'Hyiplab Plugin Inactive',
        message: 'Hyiplab plugin is not active in WordPress admin.',
        category: 'hyiplab',
        timestamp: new Date(),
        resolved: false,
        severity: 'high',
        solution: 'Go to WordPress Admin → Plugins → Activate Hyiplab'
      });
    }

    return issues;
  };

  const checkReactBuild = (): DebugError[] => {
    const issues: DebugError[] = [];
    
    // Check if React app is properly loaded
    const reactRoot = document.getElementById('root');
    if (!reactRoot || !reactRoot.children.length) {
      issues.push({
        id: 'react-not-loaded',
        type: 'error',
        title: 'React App Not Loaded',
        message: 'React application failed to load. Check build process and dist directory.',
        category: 'react',
        timestamp: new Date(),
        resolved: false,
        severity: 'critical',
        solution: 'Run "npm run build" to rebuild React app'
      });
    }

    // Check for React build errors in console
    const buildErrors = window.localStorage.getItem('react-build-errors');
    if (buildErrors) {
      issues.push({
        id: 'react-build-errors',
        type: 'error',
        title: 'React Build Errors',
        message: `Build errors detected: ${buildErrors}`,
        category: 'build',
        timestamp: new Date(),
        resolved: false,
        severity: 'high',
        solution: 'Check terminal for TypeScript/compilation errors'
      });
    }

    return issues;
  };

  const checkXAMPPStatus = async (): Promise<DebugError[]> => {
    const issues: DebugError[] = [];
    
    try {
      // Check if WordPress AJAX is available
      const ajaxUrl = window.blackCnoteApiSettings?.apiUrl || '/wp-admin/admin-ajax.php';
      
      // Try to check XAMPP status via WordPress AJAX
      const response = await fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=check_xampp_status'
      });
      
      if (!response.ok) {
        // If AJAX endpoint doesn't exist, try a simple connectivity test
        const homeResponse = await fetch('/', { method: 'HEAD' });
        if (!homeResponse.ok) {
          issues.push({
            id: 'xampp-services-down',
            type: 'error',
            title: 'XAMPP Services Down',
            message: 'Apache or MySQL services are not running. Check XAMPP control panel.',
            category: 'xampp',
            timestamp: new Date(),
            resolved: false,
            severity: 'critical',
            solution: 'Start Apache and MySQL in XAMPP Control Panel'
          });
        }
      } else {
        // Parse the response if AJAX endpoint exists
        const data = await response.json();
        if (data.success && data.data) {
          const status = data.data;
          if (status.apache?.status !== 'running') {
            issues.push({
              id: 'apache-not-running',
              type: 'error',
              title: 'Apache Not Running',
              message: status.apache?.message || 'Apache service is not running',
              category: 'xampp',
              timestamp: new Date(),
              resolved: false,
              severity: 'critical',
              solution: 'Start Apache in XAMPP Control Panel'
            });
          }
          if (status.mysql?.status !== 'running') {
            issues.push({
              id: 'mysql-not-running',
              type: 'error',
              title: 'MySQL Not Running',
              message: status.mysql?.message || 'MySQL service is not running',
              category: 'xampp',
              timestamp: new Date(),
              resolved: false,
              severity: 'critical',
              solution: 'Start MySQL in XAMPP Control Panel'
            });
          }
        }
      }
    } catch (error) {
      // If AJAX fails, try basic connectivity test
      try {
        const homeResponse = await fetch('/', { method: 'HEAD' });
        if (!homeResponse.ok) {
          issues.push({
            id: 'xampp-connection-failed',
            type: 'error',
            title: 'XAMPP Connection Failed',
            message: 'Cannot connect to XAMPP services. Check if XAMPP is running.',
            category: 'xampp',
            timestamp: new Date(),
            resolved: false,
            severity: 'critical',
            solution: 'Ensure XAMPP is installed and running on localhost'
          });
        }
      } catch (connectError) {
        issues.push({
          id: 'xampp-connection-failed',
          type: 'error',
          title: 'XAMPP Connection Failed',
          message: 'Cannot connect to XAMPP services. Check if XAMPP is running.',
          category: 'xampp',
          timestamp: new Date(),
          resolved: false,
          severity: 'critical',
          solution: 'Ensure XAMPP is installed and running on localhost'
        });
      }
    }

    return issues;
  };

  const checkFileWatching = (): DebugError[] => {
    const issues: DebugError[] = [];
    
    // Enhanced Browsersync detection - check multiple indicators
    const browsersyncIndicators = [
      // Check if we're on Browsersync port
      window.location.port === '3000' || window.location.port === '3001',
      // Check for Browsersync script injection
      document.querySelector('script[src*="browser-sync"]') !== null,
      // Check for Browsersync global object
      (window as any).__BROWSERSYNC__ !== undefined,
      // Check for Browsersync socket connection
      document.querySelector('script[src*="socket.io"]') !== null,
      // Check if we're accessing through Browsersync proxy
      window.location.hostname === 'localhost' && window.location.port === '3000',
      // Check for Browsersync in document head
      document.head.innerHTML.includes('browser-sync') || document.head.innerHTML.includes('socket.io')
    ];
    
    const browsersyncRunning = browsersyncIndicators.some(indicator => indicator);
    
    // Additional check: try to fetch Browsersync status
    const checkBrowsersyncStatus = async () => {
      try {
        const response = await fetch('http://localhost:3000/browser-sync/socket.io/', {
          method: 'GET',
          mode: 'no-cors' // Avoid CORS issues
        });
        return true;
      } catch (error) {
        return false;
      }
    };
    
    // If local detection fails, try network check
    if (!browsersyncRunning) {
      // Don't show error immediately - let the async check complete
      setTimeout(async () => {
        const networkCheck = await checkBrowsersyncStatus();
        if (networkCheck) {
          console.log('✅ Browsersync detected via network check');
          // Update the UI to reflect that Browsersync is running
          const existingError = document.querySelector('[data-error-id="browsersync-not-running"]');
          if (existingError && existingError instanceof HTMLElement) {
            existingError.style.display = 'none';
          }
        }
      }, 1000);
      
      // Only show warning if we're not on Browsersync port and no other indicators
      if (window.location.port !== '3000' && window.location.port !== '3001') {
        issues.push({
          id: 'browsersync-not-running',
          type: 'warning',
          title: 'Browsersync Not Running',
          message: 'Live editing may not work. Run "npm run dev:full" in react-app directory to start development server.',
          category: 'file-watching',
          timestamp: new Date(),
          resolved: false,
          severity: 'medium',
          solution: 'Run "cd react-app && npm run dev:full" to start the development environment'
        });
      }
    } else {
      console.log('✅ Browsersync detected and running');
    }

    return issues;
  };

  const checkIntegrationConflicts = (): DebugError[] => {
    const issues: DebugError[] = [];
    
    // Check for conflicting React Router basename using the new router config
    const currentPath = window.location.pathname;
    const currentHost = window.location.hostname;
    const currentPort = window.location.port;
    
    // Use the router configuration to determine correct basename
    let expectedBasename = '/';
    
    // If we have WordPress settings, use them
    if (window.blackCnoteApiSettings && window.blackCnoteApiSettings.baseUrl) {
      try {
        const baseUrl = new URL(window.blackCnoteApiSettings.baseUrl);
        expectedBasename = baseUrl.pathname;
      } catch (error) {
        expectedBasename = '/';
      }
    } else {
      // Fallback detection based on current environment
      if (currentHost === 'localhost') {
        if (currentPort === '8888' || currentPort === '80') {
          expectedBasename = '/'; // WordPress
        } else if (currentPort === '5174' || currentPort === '5175') {
          expectedBasename = '/'; // Vite dev server
        } else if (currentPort === '3000') {
          expectedBasename = '/'; // Browsersync
        }
      }
    }
    
    // Check if we have the router basename from our fix
    if ((window as any).blackCnoteRouterBasename) {
      expectedBasename = (window as any).blackCnoteRouterBasename;
    }
    
    // Only show conflict if we're not on the expected basename and it's not a sub-path
    const isOnExpectedPath = currentPath.startsWith(expectedBasename);
    const isRootPath = currentPath === '/' || currentPath === expectedBasename;
    
    // Don't show router conflict if we're on Browsersync (which handles routing correctly)
    if (!isOnExpectedPath && !isRootPath && currentPort !== '3000') {
      issues.push({
        id: 'router-basename-conflict',
        type: 'warning',
        title: 'React Router Basename Conflict',
        message: 'React Router basename may not match current URL structure.',
        category: 'integration',
        timestamp: new Date(),
        resolved: false,
        severity: 'medium',
        solution: 'Router configuration has been updated to handle this automatically'
      });
    }

    // Check for CORS issues - only show if not using Browsersync AND we're not on the same origin
    const isOnBrowsersync = currentPort === '3000' || currentPort === '3001';
    const isSameOrigin = window.location.origin === 'http://localhost:8888';
    
    if (!isOnBrowsersync && !isSameOrigin) {
      issues.push({
        id: 'cors-potential-issue',
        type: 'info',
        title: 'Potential CORS Issue',
        message: 'Running on different port may cause CORS issues with API calls.',
        category: 'integration',
        timestamp: new Date(),
        resolved: false,
        severity: 'low',
        solution: 'Access the site via http://localhost:3000 for best compatibility'
      });
    }

    return issues;
  };

  const checkThemeCodeConflicts = (): DebugError[] => {
    const issues: DebugError[] = [];
    
    // Check for theme-specific issues
    if (window.blackCnoteApiSettings) {
      const themeActive = window.blackCnoteApiSettings.themeActive;
      if (themeActive === false) {
        issues.push({
          id: 'theme-not-active',
          type: 'warning',
          title: 'BlackCnote Theme Not Active',
          message: 'The BlackCnote theme is not active in WordPress.',
          category: 'wordpress',
          timestamp: new Date(),
          resolved: false,
          severity: 'high',
          solution: 'Go to WordPress Admin → Appearance → Themes → Activate BlackCnote'
        });
      }
    }

    return issues;
  };

  const checkHyiplabPluginConflicts = async (): Promise<DebugError[]> => {
    const issues: DebugError[] = [];
    
    // Check for plugin-specific conflicts using our new API endpoints
    if (window.blackCnoteApiSettings) {
      try {
        // Try our new BlackCnote API endpoints first
        const apiUrl = window.blackCnoteApiSettings.apiUrl || '/wp-json/blackcnote/v1';
        const endpoints = [
          `${apiUrl}/hyiplab/status`,
          `${apiUrl}/hyiplab/health`,
          `${apiUrl}/hyiplab/test`
        ];
        
        let apiWorking = false;
        for (const endpoint of endpoints) {
          try {
            const response = await fetch(endpoint, {
              method: 'GET',
              headers: { 
                'Content-Type': 'application/json',
                'X-WP-Nonce': window.blackCnoteApiSettings.nonce || ''
              }
            });
            
            if (response.ok) {
              apiWorking = true;
              console.log('✅ HYIPLab API working via:', endpoint);
              break;
            }
          } catch (error) {
            // Continue to next endpoint
          }
        }
        
        if (!apiWorking) {
          // Try original HYIPLab endpoint as fallback
          try {
            const hyiplabResponse = await fetch('/wp-json/hyiplab/v1/status', {
              method: 'GET',
              headers: { 'Content-Type': 'application/json' }
            });
            
            if (hyiplabResponse.ok) {
              apiWorking = true;
              console.log('✅ HYIPLab API working via original endpoint');
            }
          } catch (error) {
            // Original endpoint also failed
          }
        }
        
        if (!apiWorking) {
          // Check plugin status from settings
          if (window.blackCnoteApiSettings.pluginActive === false) {
            issues.push({
              id: 'hyiplab-plugin-inactive',
              type: 'warning',
              title: 'Hyiplab Plugin Inactive',
              message: 'Hyiplab plugin is not active in WordPress admin.',
              category: 'hyiplab',
              timestamp: new Date(),
              resolved: false,
              severity: 'high',
              solution: 'Go to WordPress Admin → Plugins → Activate Hyiplab'
            });
          } else {
            issues.push({
              id: 'hyiplab-api-unavailable',
              type: 'warning',
              title: 'Hyiplab API Unavailable',
              message: 'Hyiplab plugin API endpoints are not responding.',
              category: 'hyiplab',
              timestamp: new Date(),
              resolved: false,
              severity: 'medium',
              solution: 'Check if Hyiplab plugin is properly installed and activated'
            });
          }
        }
      } catch (error) {
        // If API check fails, check plugin status from settings
        if (window.blackCnoteApiSettings.pluginActive === false) {
          issues.push({
            id: 'hyiplab-plugin-inactive',
            type: 'warning',
            title: 'Hyiplab Plugin Inactive',
            message: 'Hyiplab plugin is not active in WordPress admin.',
            category: 'hyiplab',
            timestamp: new Date(),
            resolved: false,
            severity: 'high',
            solution: 'Go to WordPress Admin → Plugins → Activate Hyiplab'
          });
        } else {
          issues.push({
            id: 'hyiplab-api-error',
            type: 'error',
            title: 'Hyiplab API Error',
            message: 'Cannot connect to Hyiplab plugin API.',
            category: 'hyiplab',
            timestamp: new Date(),
            resolved: false,
            severity: 'medium',
            solution: 'Verify plugin installation and WordPress REST API'
          });
        }
      }
    } else {
      // If no settings available, check if we can detect the plugin
      try {
        // Try our new endpoints first
        const endpoints = [
          '/wp-json/blackcnote/v1/hyiplab/status',
          '/wp-json/blackcnote/v1/hyiplab/health',
          '/wp-json/hyiplab/v1/status'
        ];
        
        let pluginDetected = false;
        for (const endpoint of endpoints) {
          try {
            const response = await fetch(endpoint);
            if (response.ok) {
              pluginDetected = true;
              console.log('✅ HYIPLab plugin detected via:', endpoint);
              break;
            }
          } catch (error) {
            // Continue to next endpoint
          }
        }
        
        if (!pluginDetected) {
          issues.push({
            id: 'hyiplab-not-available',
            type: 'info',
            title: 'Hyiplab Plugin Not Available',
            message: 'Hyiplab plugin is not installed or accessible.',
            category: 'hyiplab',
            timestamp: new Date(),
            resolved: false,
            severity: 'low',
            solution: 'Install the Hyiplab plugin for investment platform functionality'
          });
        }
      } catch (error) {
        // Plugin not available
        issues.push({
          id: 'hyiplab-not-available',
          type: 'info',
          title: 'Hyiplab Plugin Not Available',
          message: 'Hyiplab plugin is not installed or accessible.',
          category: 'hyiplab',
          timestamp: new Date(),
          resolved: false,
          severity: 'low',
          solution: 'Install the Hyiplab plugin for investment platform functionality'
        });
      }
    }

    return issues;
  };

  const runAllChecks = useCallback(async () => {
    setIsChecking(true);
    const allIssues: DebugError[] = [];
    
    try {
      // Run all diagnostic checks
      allIssues.push(...checkWordPressIntegration());
      allIssues.push(...checkReactBuild());
      allIssues.push(...await checkXAMPPStatus());
      allIssues.push(...checkFileWatching());
      allIssues.push(...checkIntegrationConflicts());
      allIssues.push(...checkThemeCodeConflicts());
      allIssues.push(...await checkHyiplabPluginConflicts());

      // Update errors state
      setErrors(allIssues);
      setLastCheck(new Date());
    } catch (error) {
      if (process.env.NODE_ENV === 'development') console.error('Debug check failed:', error);
    } finally {
      setIsChecking(false);
    }
  }, []);

  // Run checks on mount and periodically
  useEffect(() => {
    if (isDevelopment) {
      runAllChecks();
      
      // Run checks every 30 seconds in development
      const interval = setInterval(runAllChecks, 30000);
      
      return () => clearInterval(interval);
    }
  }, [isDevelopment, runAllChecks]);

  // Listen for window errors
  useEffect(() => {
    const handleError = (event: ErrorEvent) => {
      const newError: DebugError = {
        id: `js-error-${Date.now()}`,
        type: 'error',
        title: 'JavaScript Error',
        message: `${event.message} at ${event.filename}:${event.lineno}`,
        category: 'react',
        timestamp: new Date(),
        resolved: false,
        severity: 'high',
        solution: 'Check browser console for detailed error information'
      };
      
      setErrors(prev => [...prev, newError]);
    };

    const handleUnhandledRejection = (event: PromiseRejectionEvent) => {
      const newError: DebugError = {
        id: `promise-error-${Date.now()}`,
        type: 'error',
        title: 'Unhandled Promise Rejection',
        message: event.reason?.toString() || 'Unknown promise rejection',
        category: 'react',
        timestamp: new Date(),
        resolved: false,
        severity: 'high',
        solution: 'Check for async/await errors or missing error handling'
      };
      
      setErrors(prev => [...prev, newError]);
    };

    window.addEventListener('error', handleError);
    window.addEventListener('unhandledrejection', handleUnhandledRejection);
    
    return () => {
      window.removeEventListener('error', handleError);
      window.removeEventListener('unhandledrejection', handleUnhandledRejection);
    };
  }, []);

  // Don't render if not in development or no errors
  if (!isDevelopment || errors.length === 0) {
    return null;
  }

  const criticalErrors = errors.filter(e => e.severity === 'critical');
  const highWarnings = errors.filter(e => e.severity === 'high');
  const mediumWarnings = errors.filter(e => e.severity === 'medium');
  const lowInfo = errors.filter(e => e.severity === 'low');

  const getIcon = (type: string) => {
    switch (type) {
      case 'error': return <AlertTriangle className="h-4 w-4" />;
      case 'warning': return <AlertCircle className="h-4 w-4" />;
      case 'info': return <Info className="h-4 w-4" />;
      case 'success': return <CheckCircle className="h-4 w-4" />;
      default: return <Info className="h-4 w-4" />;
    }
  };

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'critical': return 'bg-red-600';
      case 'high': return 'bg-orange-500';
      case 'medium': return 'bg-yellow-500';
      case 'low': return 'bg-blue-500';
      default: return 'bg-gray-500';
    }
  };

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'error': return 'border-red-500 bg-red-50';
      case 'warning': return 'border-yellow-500 bg-yellow-50';
      case 'info': return 'border-blue-500 bg-blue-50';
      case 'success': return 'border-green-500 bg-green-50';
      default: return 'border-gray-500 bg-gray-50';
    }
  };

  return (
    <div style={{
      position: 'fixed',
      left: 0,
      right: 0,
      bottom: 0,
      zIndex: 9999,
      background: '#222',
      color: '#fff',
      fontSize: '14px',
      padding: collapsed ? '4px 12px' : '12px 24px',
      boxShadow: '0 -2px 8px rgba(0,0,0,0.1)',
      borderTop: '2px solid #ffa500',
      transition: 'all 0.2s',
      minHeight: collapsed ? '28px' : 'auto',
      cursor: 'pointer',
    }} onClick={() => setCollapsed(!collapsed)}>
      <div style={{display: 'flex', alignItems: 'center', justifyContent: 'space-between'}}>
        <span style={{fontWeight: 600, color: '#ffa500'}}>
          Debug Monitor {collapsed ? '(click to expand)' : '(click to collapse)'}
        </span>
        <span style={{fontSize: '12px', color: '#ccc'}}>
          {lastCheck.toLocaleTimeString()}
        </span>
      </div>
      {!collapsed && (
        <div style={{marginTop: 8}}>
          {errors.length === 0 ? (
            <div style={{color: '#4caf50'}}>No issues detected.</div>
          ) : (
            errors.map(err => (
              <div key={err.id} style={{marginBottom: 4, color: err.severity === 'critical' ? '#ff5252' : err.severity === 'high' ? '#ff9800' : '#fff'}}>
                <strong>{err.title}:</strong> {err.message}
              </div>
            ))
          )}
        </div>
      )}
    </div>
  );
};

export default DebugMonitor;