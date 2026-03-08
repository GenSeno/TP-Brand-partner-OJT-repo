const toggleHeader = () => {
    document.getElementById('collapse-header').classList.toggle('active');
    document.body.classList.toggle('header-collapse');
};

const classMerge = (...classes) => {
    return classes
        .flatMap((cls) => {
            if (typeof cls === 'string') {
                return cls.split(' ');
            } else if (Array.isArray(cls)) {
                return cls.map((c) => classMerge(c)).flat();
            } else if (typeof cls === 'object' && cls !== null) {
                return Object.entries(cls)
                    .filter(([, value]) => Boolean(value))
                    .map(([key]) => key);
            }
            return [];
        })
        .filter((cls, index, self) => self.indexOf(cls) === index)
        .join(' ');
};

export { classMerge, toggleHeader };
