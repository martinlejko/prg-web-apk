# "width": 600,
# 	"height": 400,
# 	"rects": [
# 		{ "left": 20, "top": 10, "width": 150, "height": 30 },
# 		{ "left": 500, "top": 50, "width": 40, "height": 300 },
# 		{ "left": 290, "top": 300, "width": 20, "height": 100 },
# 		{ "left": 50, "top": 270, "width": 80, "height": 80 }
# 	]

width = 600
height = 400
rectangles = [
    {"left": 20, "top": 10, "width": 150, "height": 30},
    {"left": 500, "top": 50, "width": 40, "height": 300},
    {"left": 290, "top": 300, "width": 20, "height": 100},
    {"left": 50, "top": 270, "width": 80, "height": 80}
]

def checkIntersection(x, y, x1, y1):
    for rect in rectangles:
        rect_left = rect['left']
        rect_top = rect['top']
        rect_width = rect['width']
        rect_height = rect['height']
        
        rect_right = rect_left + rect_width
        rect_bottom = rect_top + rect_height
        
        if not (x1 < rect_left or x > rect_right or y1 < rect_top or y > rect_bottom):
            return True  
    
    return False  


def maxFreeRectangles(height, width, rectangles):
    maxRect = {'top': -1, 'left': -1, 'widthR': 0, 'heightR': 0}

    for x in range(width):
        for y in range(height):
            for x1 in range(x + 1, width + 1):
                for y1 in range(y + 1, height + 1):
                    if not checkIntersection(x, y, x1, y1):
                        currWidth = x1
                        currHeight = y1
                        
                        if currWidth * currHeight > maxRect['widthR'] * maxRect['heightR']:
                            maxRect = {'top': y, 'left': x, 'widthR': currWidth, 'heightR': currHeight}

    return maxRect

print(maxFreeRectangles(height, width, rectangles))