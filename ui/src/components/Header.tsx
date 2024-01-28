const Header = () => {
    return (
        <header>
            <nav className="bg-white border-gray-200 px-4 lg:px-6 py-2.5 dark:bg-gray-800">
                <div className="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
                    <a
                        href="http://localhost:4040"
                        className="flex items-center"
                    >
                        <img
                            src="/src/assets/logo.png"
                            className="mr-3 h-6 sm:h-9"
                            alt="Virta Stations Logo"
                        />
                        <span className="text-3xl self-center text-xl font-semibold whitespace-nowrap dark:text-white">
                            Virta Stations
                        </span>
                    </a>
                </div>
            </nav>
        </header>
    );
};

export default Header;
