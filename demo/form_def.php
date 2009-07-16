{
  "attributes": {
    "id": "frm",
    "method": "post",
    "action": "process_form.php",
    "layout": "horizontal"
  },
  "elements": [
    {
      "type": "text",
      "label": "Stick your name in here, yo.",
      "id": "f1",
      "name": "your_name",
      "validation": [
        "required",
        "alpha"
      ],
      "filters": "striptags",
      "hint": "This is a hint. Hint, hint. It's a rather long hint. I just want to see what happens when it wraps."
    },
    {
      "type": "text",
      "label": "A different name.",
      "id": "f2",
      "name": "last_name",
      "validation": [
        {"differ": ["your_name", "your first name"]}
      ],
      "filters": "striptags"
    },
    {
      "type": "password",
      "label": "And a password too.",
      "id": "f3",
      "name": "password",
      "validation": [
        "required",
        {"rangelength": [6, 20]}
      ],
      "filters": "striptags"
    },
    {
      "type": "fieldset",
      "label": "This is a fieldset",
      "elements": [
        {
          "type": "text",
          "label": "Enter a number divisible by 5.",
          "id": "f4",
          "name": "divisible_by_5",
          "validation": [
            {"divisibleby": 5}
          ],
          "filters": "striptags"
        },
        {
          "type": "textarea",
          "label": "Write me a story. (some html allowed)",
          "id": "f5",
          "name": "story",
          "validation": [
            {"minlength": 20}
          ],
          "filters": "purify",
          "hint": "Don't make it too short, either."
        },
        {
          "type": "radiogroup",
          "id": "radiogroup1",
          "label": "Is your story any good?",
          "name": "good_story",
          "validation": "required",
          "radios": [
            {
              "id": "f6",
              "label": "Yes",
              "value": "yes"
            },
            {
              "id": "f7",
              "label": "No",
              "value": "no"
            }
          ]
        }
      ]
    },
    {
      "type": "checkbox",
      "id": "chk3",
      "name": "newsletter",
      "value": "yes",
      "label": "Sign up for newsletter?",
      "validation": "required",
      "hint": "Do you want it?"
    },
    {
      "type": "checkgroup",
      "name": "topics[]",
      "label": "Newsletter topics",
      "id": "chckgroup2",
      "validation": [
        {"selectrange": [2, 3]},
        {"requiredif": "newsletter"}
      ],
      "hint": "Select two or three items",
      "checkboxes": [
        {
          "id": "g2-1",
          "value": "one",
          "label": "one"
        },
        {
          "id": "g2-2",
          "value": "two",
          "label": "two"
        },
        {
          "id": "g2-3",
          "value": "three",
          "label": "three"
        },
        {
          "id": "g2-4",
          "value": "four",
          "label": "four"
        }
      ]
    },
    {
      "type": "select",
      "id": "slct",
      "name": "select",
      "label": "Here's a select, too.",
      "validation": [
        {"not": "default"}
      ],
      "hint": "Select a value",
      "options": [
        {
          "label": "select something...",
          "value": "default"
        },
        {
          "label": "foo",
          "value": "foo"
        },
        {
          "label": "bar",
          "value": "bar"
        },
        {
          "label": "three",
          "value": "three"
        }
      ]
    },
    {
      "label": "Submit button!",
      "type": "submit"
    }
  ]
}
