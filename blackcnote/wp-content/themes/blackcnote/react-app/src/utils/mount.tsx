import React from 'react';
import ReactDOM from 'react-dom/client';
import DebugMonitor from '../components/DebugMonitor';

interface DebugMonitorProps {
  isDevelopment: boolean;
}

const componentRegistry: { [key: string]: React.ComponentType<DebugMonitorProps> } = {
  'debug-monitor': DebugMonitor,
  // Add other components here as needed
};

export const mountComponents = () => {
  // Find all elements with the 'data-component' attribute
  const componentMountPoints = document.querySelectorAll('[data-component]');

  componentMountPoints.forEach((mountPoint) => {
    const componentName = (mountPoint as HTMLElement).dataset.component;
    if (componentName && componentRegistry[componentName]) {
      const Component = componentRegistry[componentName];
      const props = (mountPoint as HTMLElement).dataset.props 
        ? JSON.parse((mountPoint as HTMLElement).dataset.props as string)
        : {};
      
      ReactDOM.createRoot(mountPoint).render(
        <React.StrictMode>
          <Component {...props} />
        </React.StrictMode>
      );
    }
  });
}; 