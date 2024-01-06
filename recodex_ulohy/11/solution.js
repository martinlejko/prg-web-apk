function findImageGroups(imageData) {
    const imageRelations = {};
    const dataLength = imageData.length;

    for (let i = 0; i < dataLength; i++) {
        for (let j = i + 1; j < dataLength; j++) {
            const image1 = imageData[i];
            const image2 = imageData[j];
            const alike = image1.similar.includes(image2) || image2.similar.includes(image1);

            if (alike) {
                if (!imageRelations[i]) imageRelations[i] = new Set();
                if (!imageRelations[j]) imageRelations[j] = new Set();
                imageRelations[i].add(j);
                imageRelations[j].add(i);
            }
        }
    }

    const imageGroups = [];
    const visitedImages = new Array(dataLength).fill(false);

    for (let i = 0; i < dataLength; i++) {
        if (!visitedImages[i]) {
            const group = [];
            const stack = [i];

            while (stack.length > 0) {
                const currentImage = stack.pop();

                if (!visitedImages[currentImage]) {
                    visitedImages[currentImage] = true;
                    group.push(currentImage);

                    const neighbors = imageRelations[currentImage] || [];
                    stack.push(...neighbors);
                }
            }

            imageGroups.push(group);
        }
    }

    return imageGroups;
}


function combine(sorted) {
    const combinated = [];
    let curr = [];

    for (const block of sorted) {
        const isIndependent = block.every(image => image.similar.length === 0);

        if (isIndependent) {
            curr.push(...block);
        } else {
            if (curr.length > 0) {
                combinated.push([...curr]);
                curr.length = 0;
            }
            combinated.push([...block]);
        }
    }

    if (curr.length > 0) {
        combinated.push([...curr]);
    }

    return combinated;
}


function preprocessGalleryData(imageData) {
    const imgGroups = findImageGroups(imageData);
    
    const sorted = imgGroups.map((group) =>
        group
            .map((index) => imageData[index])
            .sort((img1, img2) => img1.created - img2.created)
    );

    const finalBlocks = [...sorted]; 

    finalBlocks.sort((a, b) => {
        const createdA = a[0].created;
        const createdB = b[0].created;

        return createdA < createdB ? -1 : createdA > createdB ? 1 : 0;
    });

    return combine(finalBlocks);
}



// In nodejs, this is the way how export is performed.
// In browser, module has to be a global varibale object.
module.exports = { preprocessGalleryData };
