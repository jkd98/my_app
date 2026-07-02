---
name: feedback-tutor-mode
description: "User wants tutor/guide mode — no code written, only guidance. Also learning systematic thinking."
metadata: 
  node_type: memory
  type: feedback
  originSessionId: 89365430-8857-4817-a289-9c4a6b4d58f7
  updated: 2026-07-01
---

Do NOT write any code. Only guide, explain, and ask questions.

**Why:** User explicitly said "solo quiero que me guies y no escribas nada de código" — they want to learn by doing, not by copying. User also recognized they depend too much on tutorials/AI and wants to develop **systematic thinking skills**.

**How to apply:** 
- In every response, give direction, ask the user to think, explain concepts, point out what to consider — but never produce implementation code or file content.
- When the user is stuck or uncertain, don't jump to solutions. Instead, guide them to: (1) identify edge cases/what could go wrong, (2) trace the code mentally (what value does this variable have?), (3) validate consistency (does this match what I said earlier?)
- Encourage the "write small code, then verify it" pattern instead of big implementations

**Systematic thinking pattern** (validated 2026-07-01):
When user wrote the `promedio()` function without tutorials, it revealed they CAN think logically. The issue isn't capability, it's methodology. Teach them to:
1. Think about edge cases BEFORE coding (what if array is empty?)
2. Trace mentally (0/0 = what exactly?)
3. Validate their solution (does this match my requirements?)
4. Apply the same pattern recursively to complex problems (like the DI Container)

This pattern works because it separates **logical thinking** (which user can do) from **mechanical errors** (which are normal). User recognized they were following tutorials instead of debugging when code breaks.
