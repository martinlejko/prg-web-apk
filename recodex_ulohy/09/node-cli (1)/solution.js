function maxFreeRect(width, height, rects) {
    let xCoords = [0, width];
    let yCoords = [0, height];

    rects.forEach(rect => {
        xCoords.push(rect.left, rect.left + rect.width);
        yCoords.push(rect.top, rect.top + rect.height);
    });

    xCoords = Array.from(new Set(xCoords)).sort((a, b) => a - b);
    yCoords = Array.from(new Set(yCoords)).sort((a, b) => a - b);

	let maxRect = {
        top: 0,
        left: 0,
        width: 0, 
        height: 0
    };

    for (let x = 0; x < xCoords.length; x++) {
        for (let y = 0; y < yCoords.length; y++) {
            for (let z = x + 1; z < xCoords.length; z++) {
                for (let w = y + 1; w < yCoords.length; w++) {
                    const currentRect = {
                        left: xCoords[x],
                        top: yCoords[y],
                        width: xCoords[z] - xCoords[x],
                        height: yCoords[w] - yCoords[y]
                    };

                    if (!rects.some(rect => isIntersecting(rect, currentRect))) {
                        const currArea = currentRect.width * currentRect.height;
                        const maxArea = maxRect.width * maxRect.height;

                        if (currArea > maxArea) {
                            maxRect = currentRect;
                        }
                    }
                }
            }
        }
    }
    return maxRect;
}

function isIntersecting(given, current) {
    return (
        given.left < current.left + current.width &&
        given.left + given.width > current.left &&
        given.top < current.top + current.height &&
        given.top + given.height > current.top
    );
}

module.exports = { maxFreeRect };
