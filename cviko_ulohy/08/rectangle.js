function drawAndCalcRec(width,height){

    const perimeter = 2 * (height+width);
    const area = width * height;
    console.log(`Perimeter: ${perimeter}`);
    console.log(`Area: ${area}`);

    if (width === 0 || height === 0){
        console.log("Zle parameticoucky si zadal");
        return;
    } 

    for (let row = 0; row < height; row++){
        let line ="";
        for (let col = 0; col < width;col++){
            if (row === 0 || row === height - 1){
                line+= (col === 0 || col === width - 1) ? "*" : "-";
            }
            else{
                line+= (col === 0 || col === width - 1) ? "|" : ".";
            }
        }
        console.log(line)
    }
}

drawAndCalcRec(10,20);