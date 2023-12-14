const Rectangle = {
    width: 0,
    height: 0,
    
    setDimensions: function(width, height) {
        if (width < 0 || height < 0) {
            console.log("Invalid dimensions.");
            return;
        }
        this.width = width;
        this.height = height;
    },

    calculateArea: function() {
        return this.width * this.height;
    },

    calculatePerimeter: function() {
        return 2 * (this.height + this.width);
    },

    drawRectangle: function() {
        if (this.width === 0 || this.height === 0) {
            console.log("No rectangle to draw");
            return;
        }

        for (let row = 0; row < this.height; row++) {
            let line = "";
            for (let col = 0; col < this.width; col++) {
                if (row === 0 || row === this.height - 1) {
                    line += (col === 0 || col === this.width - 1) ? "*" : "-";
                } else {
                    line += (col === 0 || col === this.width - 1) ? "|" : ".";
                }
            }
            console.log(line);
        }
    },

    printDetails: function() {
        const area = this.calculateArea();
        const perimeter = this.calculatePerimeter();
        console.log(`Width: ${this.width}`);
        console.log(`Height: ${this.height}`);
        console.log(`Area: ${area}`);
        console.log(`Perimeter: ${perimeter}`);
    }
};

// Setting dimensions and printing details
Rectangle.setDimensions(10, 20);
Rectangle.printDetails();

// Drawing the rectangle
Rectangle.drawRectangle();
