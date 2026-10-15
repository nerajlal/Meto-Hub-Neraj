# SaaS Mobile App Generation Guide

This document outlines how to generate, configure, and build React Native mobile app wrappers for new tenants (stores) on the SaaS platform.

## Directory Structure
All mobile apps are contained within the `mobile-apps` directory on the `app` branch. Each store has its own independent React Native Expo project folder:

```text
mobile-apps/
├── MetoHub/    (App for https://www.metohub.com/)
└── Chocolayt/    (App for https://store.chocolayt.com/)
```

## How to Create an App for a New Store

When a new store signs up and needs their own app, follow these steps:

### 1. Duplicate an Existing App
Copy an existing app folder to create a new one. For example, to create an app for a store named "FreshFoods":
```bash
cp -r mobile-apps/MetoHub mobile-apps/FreshFoods
```

### 2. Update the Website URL (and use the stable code)
Open the `App.js` file inside your new folder (`mobile-apps/FreshFoods/App.js`). To prevent any Android hardware crashes on emulators, make sure your code looks exactly like this, and just update the `uri`:

```javascript
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
        source={{ uri: 'https://store.freshfoods.com/' }} 
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
```

### 3. Update App Metadata
Open the `app.json` file inside your new folder (`mobile-apps/FreshFoods/app.json`) and update the following fields:
* **`name`**: The public name of the app (e.g., "Fresh Foods").
* **`slug`**: A url-friendly version of the name (e.g., "freshfoods-app").
* **`bundleIdentifier` (under `ios`)**: The unique package ID (e.g., "com.freshfoods.store").
* **`package` (under `android`)**: Must exactly match the iOS bundleIdentifier (e.g., "com.freshfoods.store").
* **`backgroundColor` (Optional)**: Update the hex codes if the store uses a different primary brand color.

### 4. Delete the Old Project ID (Crucial!)
Because you duplicated an existing app, `app.json` will contain a hidden ID linking it to the old app's EAS project. **You must delete this block** from `app.json`, or the build will fail:
```json
// Delete this section from app.json:
"extra": {
  "eas": {
    "projectId": "..."
  }
}
```
*(When you run your first build, Expo will automatically generate a brand new `projectId` for you).*

### 5. Replace Logos and Splash Screens
Navigate to the `assets` folder (`mobile-apps/FreshFoods/assets/`) and replace the default images with the store's branding:
* **`icon.png`**: The app icon (1024x1024 square image).
* **`splash.png`**: The splash screen loading image (1242x2436 image).

---

## How to Build the App

Once an app is configured, you can build it using Expo Application Services (EAS). 

### Setup Required
You must have Node.js installed. Ensure `eas-cli` is accessible by exporting your node path:
```bash
export PATH="$HOME/.nvm/versions/node/v24.18.0/bin:$PATH" 
```

### Building for Android Testing (.apk)
Navigate into the specific store's folder and run the preview build command:
```bash
cd mobile-apps/FreshFoods
npx eas-cli build --profile preview --platform android
```
*This generates an installable `.apk` file you can download straight to an Android phone.*

### Building for Production / App Stores
When you are ready to upload the app to Google Play Console and the Apple App Store, generate the optimized production bundles (`.aab` and `.ipa`):

**Android:**
```bash
npx eas-cli build --profile production --platform android
```

**iOS:**
```bash
npx eas-cli build --profile production --platform ios
```
*(Building for iOS will prompt you to log in to your Apple Developer account to configure code signing certificates automatically).*

---

### 💡 Troubleshooting Tip
If you see this error at the very end of your build: `adb executable doesn't seem to work. Please make sure Android Studio is installed`, **do not panic!** Your app build was 100% successful. This error just means you don't have a virtual Android emulator installed on your Mac to auto-open the app. You can safely ignore it and use the `.apk` link provided above the error.
