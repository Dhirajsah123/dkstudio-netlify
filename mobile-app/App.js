import React, { useState } from 'react';
import { StyleSheet, View } from 'react-native';
import LoginScreen from './screens/LoginScreen';
import RegisterScreen from './screens/RegisterScreen';
import DashboardScreen from './screens/DashboardScreen';
import TabBar from './components/TabBar';

// Placeholder for other screens
const PlaceholderScreen = ({ screenName }) => (
    <View style={styles.container}><Text>{screenName}</Text></View>
);

const MainApp = ({ user }) => {
    const [activeScreen, setActiveScreen] = useState('Dashboard');

    const renderScreen = () => {
        switch (activeScreen) {
            case 'Dashboard':
                return <DashboardScreen userId={user.id} />;
            case 'Offers':
                return <PlaceholderScreen screenName="Offers" />;
            case 'Ads':
                return <PlaceholderScreen screenName="Ads" />;
            case 'Profile':
                return <PlaceholderScreen screenName="Profile" />;
            default:
                return <DashboardScreen userId={user.id} />;
        }
    };

    return (
        <View style={styles.mainContainer}>
            <View style={styles.screenContainer}>
                {renderScreen()}
            </View>
            <TabBar activeScreen={activeScreen} onTabPress={setActiveScreen} />
        </View>
    );
};


export default function App() {
  const [user, setUser] = useState(null);
  const [showLogin, setShowLogin] = useState(true);

  if (user) {
    return <MainApp user={user} />;
  }

  if (showLogin) {
    return (
      <LoginScreen
        onLoginSuccess={setUser}
        onSwitchToRegister={() => setShowLogin(false)}
      />
    );
  } else {
    return (
      <RegisterScreen
        onSwitchToLogin={() => setShowLogin(true)}
      />
    );
  }
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  mainContainer: {
      flex: 1,
  },
  screenContainer: {
      flex: 1,
  }
});
