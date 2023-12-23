def maxFreeRect(width, height, rects):
    events = []

    for rect in rects:
        events.append((rect['left'], True, rect))
        events.append((rect['left'] + rect['width'], False, rect))

    events.sort(key=lambda x: (x[0], not x[1]))

    maxRect = {
        'top': 0,
        'left': 0,
        'width': 0,
        'height': 0
    }

    active_rects = set()

    prev_x = 0

    for x, is_start, rect in events:
        interval_width = x - prev_x

        max_height = 0
        for active_rect in active_rects:
            max_height = max(max_height, active_rect['top'] + active_rect['height'])

        potential_rect = {
            'left': prev_x,
            'top': 0,
            'width': interval_width,
            'height': max_height
        }

        potential_area = potential_rect['width'] * potential_rect['height']
        max_area = maxRect['width'] * maxRect['height']

        if potential_area > max_area:
            maxRect = potential_rect

        if is_start:
            active_rects.add(rect)
        else:
            active_rects.remove(rect)

        prev_x = x

    return maxRect
