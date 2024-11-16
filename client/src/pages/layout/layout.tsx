import React from 'react';
import Footer from './footer/footer';
import HeaderPage from './header/headerPage';

interface LayoutProps {
    children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
    return (
        <div>
            <HeaderPage/>
                {children}
            <Footer/>
        </div>
    );
};

export default Layout;