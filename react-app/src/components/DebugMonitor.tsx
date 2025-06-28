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
    
    // Check if Browsersync is running
    const browsersyncRunning = window.location.port === '3000';
    if (!browsersyncRunning) {
      issues.push({
        id: 'browsersync-not-running',
        type: 'warning',
        title: 'Browsersync Not Running',
        message: 'Live editing may not work. Run "npm run dev:full" to start development server.',
        category: 'file-watching',
        timestamp: new Date(),
        resolved: false,
        severity: 'medium',
        solution: 'Run "npm run dev:full" to start the development environment'
      });
    }

    return issues;
  };

  const checkIntegrationConflicts = (): DebugError[] => {
    const issues: DebugError[] = [];
    
    // Check for conflicting React Router basename
    if (window.blackCnoteApiSettings && window.blackCnoteApiSettings.baseUrl !== window.location.pathname) {
      issues.push({
        id: 'router-basename-conflict',
        type: 'warning',
        title: 'React Router Basename Conflict',
        message: 'React Router basename may not match current URL structure.',
        category: 'integration',
        timestamp: new Date(),
        resolved: false,
        severity: 'medium',
        solution: 'Check src/config/environment.ts for correct baseUrl configuration'
      });
    }

    // Check for CORS issues
    if (window.location.origin !== 'http://localhost:3000') {
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
    
    // Check for plugin-specific conflicts
    if (window.blackCnoteApiSettings) {
      try {
        // Check if plugin API endpoints are accessible
        const apiUrl = window.blackCnoteApiSettings.apiUrl || '/wp-json';
        const response = await fetch(`${apiUrl}/hyiplab/v1/status`, {
          method: 'GET',
          headers: { 'Content-Type': 'application/json' }
        });
        
        if (!response.ok) {
          // Try alternative endpoint or check if plugin is active
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
        const response = await fetch('/wp-json/hyiplab/v1/status');
        if (!response.ok) {
          issues.push({
            id: 'hyiplab-not-installed',
            type: 'info',
            title: 'Hyiplab Plugin Not Detected',
            message: 'Hyiplab plugin may not be installed or activated.',
            category: 'hyiplab',
            timestamp: new Date(),
            resolved: false,
            severity: 'low',
            solution: 'Install and activate the Hyiplab plugin for full functionality'
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
    <div className="fixed top-0 left-0 right-0 z-50">
      {/* Summary Banner */}
      <div className={`${criticalErrors.length > 0 ? 'bg-red-600' : highWarnings.length > 0 ? 'bg-orange-500' : 'bg-yellow-500'} text-white px-4 py-2 shadow-lg`}>
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <AlertTriangle className="h-5 w-5" />
            <div>
              <span className="font-semibold">Debug Monitor Active</span>
              <span className="ml-2 text-sm opacity-90">
                {criticalErrors.length} Critical, {highWarnings.length} High, {mediumWarnings.length} Medium, {lowInfo.length} Low
              </span>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <span className="text-sm opacity-90">
              Last check: {lastCheck.toLocaleTimeString()}
            </span>
            <button
              onClick={() => setIsExpanded(!isExpanded)}
              className="px-3 py-1 bg-white bg-opacity-20 rounded hover:bg-opacity-30 transition-colors"
            >
              {isExpanded ? 'Hide Details' : 'Show Details'}
            </button>
            <button
              onClick={runAllChecks}
              disabled={isChecking}
              className="px-3 py-1 bg-white bg-opacity-20 rounded hover:bg-opacity-30 transition-colors disabled:opacity-50 flex items-center space-x-1"
            >
              <RefreshCw className={`h-4 w-4 ${isChecking ? 'animate-spin' : ''}`} />
              <span>Refresh</span>
            </button>
          </div>
        </div>
      </div>

      {/* Detailed Error List */}
      {isExpanded && (
        <div className="bg-white border-b border-gray-200 max-h-96 overflow-y-auto">
          <div className="p-4 space-y-3">
            {errors.map((error) => (
              <div
                key={error.id}
                className={`border-l-4 p-3 rounded-r ${getTypeColor(error.type)}`}
              >
                <div className="flex items-start justify-between">
                  <div className="flex items-start space-x-3 flex-1">
                    <div className={`mt-1 p-1 rounded ${getSeverityColor(error.severity)}`}>
                      {getIcon(error.type)}
                    </div>
                    <div className="flex-1">
                      <div className="flex items-center space-x-2">
                        <h4 className="font-semibold text-gray-900">{error.title}</h4>
                        <span className={`px-2 py-1 text-xs rounded-full ${getSeverityColor(error.severity)} text-white`}>
                          {error.severity.toUpperCase()}
                        </span>
                        <span className="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700">
                          {error.category}
                        </span>
                      </div>
                      <p className="text-sm text-gray-700 mt-1">{error.message}</p>
                      {error.solution && (
                        <p className="text-sm text-green-700 mt-1 font-medium">
                          💡 Solution: {error.solution}
                        </p>
                      )}
                      <p className="text-xs text-gray-500 mt-1">
                        {error.timestamp.toLocaleString()}
                      </p>
                    </div>
                  </div>
                  <button
                    onClick={() => setErrors(prev => prev.filter(e => e.id !== error.id))}
                    className="text-gray-400 hover:text-gray-600"
                  >
                    <X className="h-4 w-4" />
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export default DebugMonitor;