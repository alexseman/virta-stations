const Footer = () => {
    return (
        <footer className="sticky top-[100vh] p-4 bg-white md:p-8 lg:p-10 dark:bg-gray-800">
            <div className="mx-auto max-w-screen-xl text-center">
                <a
                    href="http://localhost:4040"
                    className="flex justify-center items-center text-2xl font-semibold text-gray-900 dark:text-white"
                >
                    <img
                        src="/src/assets/logo.png"
                        className="mr-3 h-6 sm:h-9"
                        alt="Virta Stations Logo"
                    />
                    Virta Stations
                </a>
                <p className="my-6 text-gray-500 dark:text-gray-400">
                    Build your smart electric vehicle charging business fast and
                    cost-effectively with the Virta end-to-end charging
                    solution.
                </p>
                <span className="text-sm text-gray-500 sm:text-center dark:text-gray-400">
                    &copy; 2014&ndash;{new Date().getFullYear()}&nbsp;
                    <a href="http://localhost:4040" className="hover:underline">
                        Virta Global&trade;
                    </a>
                </span>
            </div>
        </footer>
    );
};

export default Footer;
