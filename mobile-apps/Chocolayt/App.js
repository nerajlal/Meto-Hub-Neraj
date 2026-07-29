import { SafeAreaView, StyleSheet, StatusBar, Platform, BackHandler } from 'react-native';
import { WebView } from 'react-native-webview';
import { useRef, useEffect, useState } from 'react';

export default function App() {
  const webViewRef = useRef(null);
  const canGoBackRef = useRef(false);

  useEffect(() => {
    const onBackPress = () => {
      if (canGoBackRef.current && webViewRef.current) {
        webViewRef.current.goBack();
        return true; // Prevent app from exiting, go back in web history instead
      }
      return false; // No more web history, allow app to exit
    };

    BackHandler.addEventListener('hardwareBackPress', onBackPress);
    return () => BackHandler.removeEventListener('hardwareBackPress', onBackPress);
  }, []);

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0F172A" />
      <WebView 
        ref={webViewRef}
        source={{ uri: 'https://store.chocolayt.com/' }} 
        style={styles.webview} 
        originWhitelist={['*']}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        androidLayerType="hardware" 
        applicationNameForUserAgent="GrocerySaaSApp"
        onNavigationStateChange={(navState) => { canGoBackRef.current = navState.canGoBack; }}
        allowsBackForwardNavigationGestures={true}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingTop: Platform.OS === 'android' ? StatusBar.currentHeight : 0,
  },
  webview: {
    flex: 1,
  },
});
