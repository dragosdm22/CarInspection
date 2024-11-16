
import './App.css';
import LoginPage from './pages/login/loginPage';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { AuthContext, AuthProvider } from './contexts/authContext';

function App() {
  const {  i18n: {language} } = useTranslation();

  return (
    <div className="App">
      <AuthProvider>
   <ToastContainer
        position="top-right"
        autoClose={5000}
        hideProgressBar={false}
        newestOnTop
        closeOnClick
        rtl={false}
        pauseOnFocusLoss
        draggable
        pauseOnHover
        theme="light"
      />
      <LoginPage />
      </AuthProvider>
    </div>
  );
 }

export default App;
