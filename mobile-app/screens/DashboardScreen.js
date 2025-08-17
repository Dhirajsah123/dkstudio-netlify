import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, ActivityIndicator, Alert } from 'react-native';

const DashboardScreen = ({ userId }) => {
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await fetch(`http://localhost/cashmitra/php/api/dashboard.php?user_id=${userId}`);
        const result = await response.json();
        if (result.success) {
          setData(result.data);
        } else {
          Alert.alert('Error', result.message);
        }
      } catch (error) {
        console.error(error);
        Alert.alert('Error', 'Failed to fetch dashboard data.');
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [userId]);

  if (loading) {
    return <ActivityIndicator size="large" style={styles.loader} />;
  }

  if (!data) {
    return <Text>No data found.</Text>;
  }

  const { user, offerwallProviders, recentTransactions, referralSummary } = data;

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.welcome}>Welcome, {user.username}!</Text>

      {/* Balance Cards */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Your Balance</Text>
        <View style={styles.card}>
            <Text>Main Balance: {user.balance} Coins</Text>
        </View>
      </View>

      {/* Offerwalls Section */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Offerwalls</Text>
        {offerwallProviders.map(provider => (
          <View key={provider.id} style={styles.card}>
            <Text>{provider.name}</Text>
          </View>
        ))}
      </View>

      {/* Recent Transactions */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Recent Transactions</Text>
        {recentTransactions.map(tx => (
          <View key={tx.id} style={styles.card}>
            <Text>{tx.description} - {tx.amount} points</Text>
          </View>
        ))}
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 10,
    backgroundColor: '#f0f2f5',
  },
  loader: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  welcome: {
    fontSize: 22,
    fontWeight: 'bold',
    marginBottom: 20,
  },
  section: {
    marginBottom: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  card: {
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 10,
    marginBottom: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.2,
    shadowRadius: 1.41,
    elevation: 2,
  },
});

export default DashboardScreen;
