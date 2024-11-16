import { FC } from "react";
import { brandName } from "../../constants/constants";
import LanguageSelector from "./languageSelector";

const Header: FC = () => {
    return (
        <nav className="fixed w-full top-0 border-b border-gray-300 h-14 flex items-center justify-between" style={{ background: 'linear-gradient(to right, #4CAF50, #2E7D32)' }}>
            <div className="container mx-auto flex justify-between items-center">
                <div className="text-gray-100 text-xl font-bold">
                    {brandName}
                </div>
                <div className="flex items-center space-x-4">
                    <a href="#" className="text-gray-100 hover:text-black hidden md:inline">Home</a>
                    <a href="#" className="text-gray-100 hover:text-black hidden md:inline">About</a>
                    <a href="#" className="text-gray-100 hover:text-black hidden md:inline">Services</a>
                    <a href="#" className="text-gray-100 hover:text-black hidden md:inline">Contact</a>
                    <LanguageSelector />
                </div>
            </div>
        </nav>
    );
};

export default Header;
